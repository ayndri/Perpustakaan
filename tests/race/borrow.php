<?php

// Dijalankan RaceTest sebagai proses PHP terpisah: satu mahasiswa mencoba memesan satu buku.
// Semua proses menunggu sampai detik yang sama ($argv[3]) lalu menembak bersamaan.

use App\Exceptions\CirculationException;
use App\Models\Book;
use App\Models\Student;
use App\Services\Circulation;
use Illuminate\Support\Facades\DB;

require __DIR__.'/../../vendor/autoload.php';
$app = require __DIR__.'/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

[, $studentId, $bookId, $startAt] = $argv;

// Buka koneksi dulu supaya yang balapan benar-benar query-nya, bukan waktu konek.
DB::connection()->getPdo();
$student = Student::findOrFail($studentId);
$book = Book::findOrFail($bookId);

while (microtime(true) < (float) $startAt) {
    usleep(500);
}

try {
    app(Circulation::class)->requestLoan($student, $book);
    echo 'ok';
} catch (CirculationException) {
    echo 'ditolak';
}
