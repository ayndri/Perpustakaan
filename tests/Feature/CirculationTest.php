<?php

namespace Tests\Feature;

use App\Exceptions\CirculationException;
use App\Models\Borrowing;
use App\Notifications\TicketReady;
use App\Services\Circulation;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class CirculationTest extends TestCase
{
    use RefreshDatabase;

    private Circulation $circulation;

    protected function setUp(): void
    {
        parent::setUp();
        $this->circulation = app(Circulation::class);
    }

    public function test_eksemplar_terakhir_hanya_bisa_dipesan_satu_orang(): void
    {
        $book = $this->book(['stock' => 1]);

        $this->circulation->requestLoan($this->student(), $book);

        $this->expectException(CirculationException::class);
        try {
            $this->circulation->requestLoan($this->student(), $book);
        } finally {
            $this->assertSame(0, $book->fresh()->stock);
            $this->assertSame(1, Borrowing::count());
        }
    }

    public function test_database_menolak_stok_minus(): void
    {
        $book = $this->book(['stock' => 0]);

        $this->expectException(QueryException::class);
        $book->decrement('stock');
    }

    public function test_mahasiswa_belum_terverifikasi_tidak_bisa_meminjam(): void
    {
        $this->expectExceptionMessage('Verifikasi KTM');
        $this->circulation->requestLoan($this->student(['verification_status' => 'pending']), $this->book());
    }

    public function test_tiket_yang_tidak_diambil_kedaluwarsa_dan_stok_kembali(): void
    {
        $book = $this->book(['stock' => 1]);
        $ticket = $this->circulation->requestLoan($this->student(), $book);

        $this->travel(config('library.pickup_hours') + 1)->hours();
        $this->circulation->sweep();

        $this->assertSame('expired', $ticket->fresh()->status);
        $this->assertSame(1, $book->fresh()->stock);
    }

    public function test_buku_yang_kembali_diberikan_ke_antrean_terdepan_bukan_ke_rak(): void
    {
        Notification::fake();
        $book = $this->book(['stock' => 1]);
        [$a, $b, $c] = [$this->student(), $this->student(), $this->student()];

        $loan = $this->circulation->handOver($this->circulation->requestLoan($a, $book));
        $first = $this->circulation->reserve($b, $book);
        $this->travel(1)->minutes();
        $second = $this->circulation->reserve($c, $book);
        $this->assertSame(2, $second->position());

        $this->circulation->receiveReturn($loan);

        $this->assertSame(0, $book->fresh()->stock, 'eksemplar tidak boleh masuk rak selama ada antrean');
        $this->assertSame('fulfilled', $first->fresh()->status);
        $this->assertSame('pending', $first->fresh()->borrowing->status);
        $this->assertSame(1, $second->fresh()->position());
        Notification::assertSentTo($b, TicketReady::class);
    }

    public function test_tiket_antrean_yang_hangus_pindah_ke_orang_berikutnya(): void
    {
        Notification::fake();
        $book = $this->book(['stock' => 1]);
        [$a, $b, $c] = [$this->student(), $this->student(), $this->student()];

        $loan = $this->circulation->handOver($this->circulation->requestLoan($a, $book));
        $this->circulation->reserve($b, $book);
        $this->travel(1)->minutes();
        $next = $this->circulation->reserve($c, $book);
        $this->circulation->receiveReturn($loan);

        $this->travel(config('library.reservation_pickup_hours') + 1)->hours();
        $this->circulation->sweep();

        $this->assertSame('fulfilled', $next->fresh()->status);
        $this->assertSame('pending', $next->fresh()->borrowing->status);
        $this->assertSame(0, $book->fresh()->stock);
        Notification::assertSentTo($c, TicketReady::class);
    }

    public function test_denda_dihitung_per_hari_dan_memblokir_peminjaman_sampai_lunas(): void
    {
        $student = $this->student();
        $loan = $this->circulation->handOver($this->circulation->requestLoan($student, $this->book()));

        $this->travelTo($loan->due_at->copy()->addDays(3)->setTime(10, 0));
        $this->assertSame(3 * config('library.fine_per_day'), $loan->fresh()->currentFine());

        $returned = $this->circulation->receiveReturn($loan);
        $this->assertSame(3 * config('library.fine_per_day'), $returned->fine_amount);

        try {
            $this->circulation->requestLoan($student, $this->book());
            $this->fail('peminjaman seharusnya diblokir selama denda belum lunas');
        } catch (CirculationException $e) {
            $this->assertStringContainsString('denda', $e->getMessage());
        }

        $this->circulation->markFinePaid($returned);
        $this->assertSame('pending', $this->circulation->requestLoan($student, $this->book())->status);
    }

    public function test_dikembalikan_sebelum_jatuh_tempo_tidak_kena_denda(): void
    {
        $loan = $this->circulation->handOver($this->circulation->requestLoan($this->student(), $this->book()));
        $this->travelTo($loan->due_at->copy()->subHour());

        $this->assertSame(0, $this->circulation->receiveReturn($loan)->fine_amount);
    }

    public function test_perpanjangan_hanya_sekali(): void
    {
        $student = $this->student();
        $loan = $this->circulation->handOver($this->circulation->requestLoan($student, $this->book()));
        $due = $loan->due_at->copy();

        $renewed = $this->circulation->renew($student, $loan);
        $this->assertTrue($renewed->due_at->equalTo($due->addDays(config('library.renew_days'))));

        $this->expectExceptionMessage('jatah perpanjangan');
        $this->circulation->renew($student, $renewed);
    }

    public function test_tidak_bisa_diperpanjang_kalau_ada_yang_antre(): void
    {
        $book = $this->book(['stock' => 1]);
        $student = $this->student();
        $loan = $this->circulation->handOver($this->circulation->requestLoan($student, $book));
        $this->circulation->reserve($this->student(), $book);

        $this->expectExceptionMessage('antre');
        $this->circulation->renew($student, $loan);
    }

    public function test_tidak_bisa_diperpanjang_kalau_sudah_telat(): void
    {
        $student = $this->student();
        $loan = $this->circulation->handOver($this->circulation->requestLoan($student, $this->book()));
        $this->travelTo($loan->due_at->copy()->addDay());

        $this->expectExceptionMessage('lewat jatuh tempo');
        $this->circulation->renew($student, $loan);
    }

    public function test_menolak_tiket_dua_kali_tidak_menggandakan_stok(): void
    {
        $book = $this->book(['stock' => 1]);
        $ticket = $this->circulation->requestLoan($this->student(), $book);

        $this->circulation->reject($ticket);
        try {
            $this->circulation->reject($ticket);
        } catch (CirculationException) {
        }

        $this->assertSame(1, $book->fresh()->stock);
    }

    public function test_eksemplar_baru_melayani_antrean_dulu_sisanya_ke_rak(): void
    {
        Notification::fake();
        $book = $this->book(['stock' => 1]);
        $this->circulation->handOver($this->circulation->requestLoan($this->student(), $book));
        $waiting = $this->circulation->reserve($this->student(), $book);

        $this->circulation->restock($book, 3);

        $this->assertSame('fulfilled', $waiting->fresh()->status);
        $this->assertSame(2, $book->fresh()->stock, 'satu dari tiga eksemplar baru jadi tiket antrean');
    }

    public function test_ebook_otomatis_selesai_setelah_masa_pinjam(): void
    {
        $book = $this->book(['digital_link' => 'https://example.org/buku', 'stock_online' => 1]);
        $loan = $this->circulation->borrowEbook($this->student(), $book);
        $this->assertSame(0, $book->fresh()->stock_online);

        $this->travel(config('library.ebook_days'))->days();
        $this->travel(1)->minutes();
        $this->circulation->sweep();

        $this->assertSame('returned', $loan->fresh()->status);
        $this->assertSame(1, $book->fresh()->stock_online);
    }
}
