<?php

namespace Tests\Feature;

use App\Models\Borrowing;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

/**
 * Bukti bahwa lock bekerja di database sungguhan, bukan hanya di urutan kode:
 * sepuluh proses PHP terpisah memesan satu eksemplar terakhir pada saat yang sama.
 */
class RaceTest extends TestCase
{
    // Data harus benar-benar ter-commit supaya terlihat oleh proses lain,
    // jadi tidak bisa memakai transaksi RefreshDatabase.
    use DatabaseMigrations;

    private const RACERS = 10;

    public function test_sepuluh_permintaan_serentak_untuk_eksemplar_terakhir_hanya_satu_yang_berhasil(): void
    {
        $book = $this->book(['stock' => 1]);
        $students = collect(range(1, self::RACERS))->map(fn () => $this->student());

        $startAt = microtime(true) + 6;
        $env = array_merge(getenv(), [
            'APP_ENV' => 'testing',
            'DB_CONNECTION' => config('database.default'),
            'DB_DATABASE' => config('database.connections.pgsql.database'),
        ]);

        $processes = $students->map(function ($student) use ($book, $startAt, $env) {
            $cmd = [PHP_BINARY, base_path('tests/race/borrow.php'), (string) $student->id, (string) $book->id, (string) $startAt];
            $proc = proc_open($cmd, [1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes, base_path(), $env);

            return [$proc, $pipes];
        });

        $results = $processes->map(function ($p) {
            [$proc, $pipes] = $p;
            $out = stream_get_contents($pipes[1]);
            $err = stream_get_contents($pipes[2]);
            proc_close($proc);

            // Baris terakhir saja: PHP bisa mencetak peringatan startup ke stdout sebelum hasilnya.
            $last = trim(collect(preg_split('/\R/', trim($out)))->last() ?? '');

            return in_array($last, ['ok', 'ditolak'], true) ? $last : 'error: '.trim($out.' '.$err);
        })->countBy();

        $summary = 'hasil per proses: '.json_encode($results);
        $this->assertSame(1, $results['ok'] ?? 0, $summary);
        $this->assertSame(self::RACERS - 1, $results['ditolak'] ?? 0, $summary);
        $this->assertSame(0, $book->fresh()->stock);
        $this->assertSame(1, Borrowing::where('book_id', $book->id)->count());
    }
}
