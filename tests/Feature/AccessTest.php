<?php

namespace Tests\Feature;

use App\Services\Circulation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_tamu_yang_membuka_halaman_admin_diarahkan_ke_login_admin(): void
    {
        $this->get('/admin/dashboard')->assertRedirect(route('admin.login'));
    }

    public function test_tamu_yang_meminjam_diarahkan_ke_login_mahasiswa(): void
    {
        $this->post(route('borrow.store', $this->book()))->assertRedirect(route('login'));
    }

    public function test_mahasiswa_tidak_bisa_membuka_ktm_orang_lain(): void
    {
        $other = $this->student(['ktm_image' => 'private:ktm/rahasia.png']);

        $this->actingAs($this->student(), 'student')
            ->get(route('admin.students.ktm', $other))
            ->assertRedirect(route('admin.login'));
    }

    public function test_tiket_orang_lain_tidak_bisa_dibuka(): void
    {
        $ticket = app(Circulation::class)->requestLoan($this->student(), $this->book());

        $this->actingAs($this->student(), 'student')
            ->get(route('borrowings.show', $ticket))
            ->assertNotFound();
    }

    public function test_meminjam_lewat_web_membuat_tiket_dan_membuka_halaman_tiket(): void
    {
        $student = $this->student();
        $book = $this->book(['stock' => 2]);

        $response = $this->actingAs($student, 'student')->post(route('borrow.store', $book));

        $ticket = $student->borrowings()->first();
        $response->assertRedirect(route('borrowings.show', $ticket));
        $this->get(route('borrowings.show', $ticket))->assertOk()->assertSee($ticket->ticket_number);
    }

    public function test_aturan_yang_dilanggar_kembali_ke_halaman_dengan_pesan(): void
    {
        $book = $this->book(['stock' => 0]);

        $this->actingAs($this->student(), 'student')
            ->from(route('books.show', $book))
            ->post(route('borrow.store', $book))
            ->assertRedirect(route('books.show', $book))
            ->assertSessionHas('error');
    }

    public function test_upload_yang_gagal_kembali_ke_formulir_dengan_pesan(): void
    {
        // Konfigurasi yang salah ketik (misalnya hanya API secret) tidak boleh berujung 500.
        config(['services.cloudinary.url' => 'hanya-secret-tanpa-format']);
        Storage::fake('local');
        Storage::disk('local')->put('ktm/lama.png', 'isi');
        $student = $this->student(['verification_status' => 'rejected', 'ktm_image' => 'private:ktm/lama.png']);

        $this->actingAs($student, 'student')
            ->from(route('verification.index'))
            ->post(route('verification.store'), ['ktm_image' => UploadedFile::fake()->image('ktm.png')])
            ->assertRedirect(route('verification.index'))
            ->assertSessionHas('error');

        // KTM lama tidak boleh ikut hilang hanya karena upload yang baru gagal.
        $this->assertSame('private:ktm/lama.png', $student->fresh()->ktm_image);
        Storage::disk('local')->assertExists('ktm/lama.png');
    }

    public function test_admin_menyerahkan_dan_menerima_buku_lewat_meja(): void
    {
        $ticket = app(Circulation::class)->requestLoan($this->student(), $this->book());
        $admin = $this->admin();

        $this->actingAs($admin)->get(route('admin.desk.lookup', ['kode' => strtolower($ticket->ticket_number)]))
            ->assertOk()
            ->assertSee('Serahkan buku');

        $this->actingAs($admin)->post(route('admin.borrowings.hand-over', $ticket))->assertSessionHas('success');
        $this->assertSame('active', $ticket->fresh()->status);

        $this->actingAs($admin)->post(route('admin.borrowings.receive', $ticket))->assertSessionHas('success');
        $this->assertSame('returned', $ticket->fresh()->status);
    }
}
