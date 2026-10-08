<?php

namespace Database\Seeders;

use App\Exceptions\CirculationException;
use App\Models\Book;
use App\Models\BookRequest;
use App\Models\Category;
use App\Models\Review;
use App\Models\Student;
use App\Models\User;
use App\Services\Circulation;
use App\Support\Media;
use Illuminate\Database\Seeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;

/**
 * Data demo. Buku dan ISBN-nya nyata (lihat data/books.php); anggota, riwayat pinjam,
 * dan ulasan fiktif. Semua riwayat dibuat lewat Circulation dengan jam yang diputar
 * mundur, jadi stok, status, dan denda konsisten dengan aturan aplikasi.
 */
class DatabaseSeeder extends Seeder
{
    private Circulation $circulation;

    private const REVIEWS_STUDY = [
        'Jadi pegangan waktu ngerjain tugas besar. Contohnya relevan.',
        'Penjelasannya runtut, cocok dibaca sambil ikut kuliah.',
        'Beberapa bab berat, tapi worth it kalau dibaca pelan-pelan.',
        'Lebih enak dipakai sebagai referensi daripada dibaca dari depan sampai belakang.',
        'Akhirnya paham konsep yang di kelas cuma dibahas sekilas.',
        'Latihannya bagus, tapi butuh waktu lama untuk menyelesaikan semuanya.',
        'Bahasanya ringan untuk ukuran buku teknis.',
        'Bagus, tapi contoh kodenya sudah agak ketinggalan versi.',
        'Wajib baca sebelum magang.',
        'Bab awal cukup untuk kebutuhanku; sisanya kubaca sekilas.',
    ];

    private const REVIEWS_READ = [
        'Selesai dalam dua malam. Tidak bisa berhenti.',
        'Awalnya lambat, setelah sepertiga buku jadi sulit diletakkan.',
        'Endingnya masih kepikiran sampai sekarang.',
        'Tokoh-tokohnya terasa hidup.',
        'Bagus, tapi beberapa bagian terasa terlalu panjang.',
        'Cocok dibaca di sela jadwal kuliah yang padat.',
        'Ini kedua kalinya aku baca, tetap dapat hal baru.',
        'Rekomendasi buat yang baru mulai suka baca.',
    ];

    private const STUDY_CATEGORIES = ['Rekayasa Perangkat Lunak', 'Algoritma & Ilmu Komputer', 'Sistem, Jaringan & Basis Data', 'Kecerdasan Buatan', 'Desain & UX'];

    public function run(Circulation $circulation): void
    {
        $this->circulation = $circulation;
        mt_srand(2026);

        User::create(['name' => 'Petugas Perpustakaan', 'email' => 'admin@perpus.test', 'password' => 'password123']);

        // --- Anggota ---------------------------------------------------------------
        $student = fn (string $nim, string $name, string $major, string $gender, string $status = 'verified', ?string $email = null) => Student::create([
            'nim' => $nim, 'name' => $name, 'jurusan' => $major, 'gender' => $gender,
            'email' => $email ?? strtolower(str_replace(' ', '.', $name)).'@mhs.test',
            'password' => 'password123', 'verification_status' => $status,
        ]);

        $dewi = $student('1462300065', 'Dewi Nur Ayundari', 'Teknik Informatika', 'P', email: 'dewi@mhs.test');
        $budi = $student('1462300012', 'Budi Santoso', 'Sistem Informasi', 'L', email: 'budi@mhs.test');
        $rani = $student('1462300031', 'Rani Puspita', 'Teknik Informatika', 'P', email: 'rani@mhs.test');
        $student('1462300047', 'Fajar Nugroho', 'Manajemen', 'L', 'none', 'fajar@mhs.test');

        $readers = collect([
            ['1462300078', 'Sari Wulandari', 'Sistem Informasi', 'P'], ['1462300083', 'Andi Pratama', 'Teknik Informatika', 'L'],
            ['1462300091', 'Nadia Putri', 'Teknik Informatika', 'P'], ['1462300104', 'Yoga Saputra', 'Manajemen', 'L'],
            ['1462300112', 'Intan Permatasari', 'Sastra Indonesia', 'P'], ['1462300125', 'Rizky Maulana', 'Teknik Informatika', 'L'],
            ['1462300133', 'Ayu Lestari', 'Desain Komunikasi Visual', 'P'], ['1462300147', 'Bagas Wicaksono', 'Teknik Elektro', 'L'],
            ['1462300152', 'Citra Anggraini', 'Akuntansi', 'P'], ['1462300168', 'Dimas Aditya', 'Sistem Informasi', 'L'],
            ['1462300171', 'Eka Safitri', 'Psikologi', 'P'], ['1462300186', 'Galih Ramadhan', 'Teknik Informatika', 'L'],
            ['1462300194', 'Hana Kartika', 'Sastra Inggris', 'P'], ['1462300207', 'Ilham Fauzi', 'Manajemen', 'L'],
            ['1462300215', 'Juwita Sari', 'Teknik Informatika', 'P'], ['1462300223', 'Kevin Hartono', 'Teknik Industri', 'L'],
        ])->map(fn ($r) => $student(...$r))->push($budi, $rani)->values();

        // --- Koleksi ---------------------------------------------------------------
        $data = require __DIR__.'/data/books.php';
        $categories = collect($data)->pluck(5)->unique()->sort()
            ->mapWithKeys(fn ($name) => [$name => Category::create(['name' => $name])->id]);

        $books = collect($data)->mapWithKeys(fn ($b) => [$b[0] => Book::create([
            'isbn' => $b[0], 'title' => $b[1], 'author' => $b[2], 'publisher' => $b[3], 'year' => $b[4],
            'category_id' => $categories[$b[5]], 'stock' => $b[6], 'floor' => $b[7], 'shelf_code' => $b[8],
            'description' => $b[9], 'digital_link' => $b[10] ?? null, 'stock_online' => $b[11] ?? 0,
            'cover' => "https://covers.openlibrary.org/b/isbn/{$b[0]}-L.jpg",
        ])]);
        $studyCategoryIds = collect(self::STUDY_CATEGORIES)->map(fn ($c) => $categories[$c] ?? null)->filter();

        // --- Riwayat baca 5 bulan terakhir, dengan ulasan ----------------------------
        // Tiap buku dibaca beberapa kali secara bergiliran (tidak tumpang tindih),
        // dikembalikan tepat waktu, lalu sebagian pembacanya menulis ulasan.
        $reviewed = [];
        foreach ($books->values() as $b => $book) {
            if ($book->stock < 1) {
                continue;
            }
            $quality = [3, 4, 4, 4, 5, 5][mt_rand(0, 5)];
            $rounds = mt_rand(2, 6);
            $start = now()->subDays(150 - ($b * 7) % 50)->setTime(8 + $b % 8, ($b * 13) % 60);

            for ($j = 0; $j < $rounds; $j++) {
                $reader = $readers[mt_rand(0, $readers->count() - 1)];
                $borrowedAt = $start->copy()->addDays($j * 11);
                $returnedAt = $borrowedAt->copy()->addDays(mt_rand(2, 6))->setTime(mt_rand(9, 15), mt_rand(0, 59));
                if ($returnedAt->isAfter(now()->subDays(20))) {
                    break;
                }

                $loan = $this->attempt($borrowedAt, fn () => $this->circulation->handOver($this->circulation->requestLoan($reader, $book)));
                if (! $loan) {
                    continue;
                }
                $this->attempt($returnedAt, fn () => $this->circulation->receiveReturn($loan));

                $key = $reader->id.'-'.$book->id;
                if (mt_rand(1, 100) <= 70 && ! isset($reviewed[$key])) {
                    $reviewed[$key] = true;
                    $pool = $studyCategoryIds->contains($book->category_id) ? self::REVIEWS_STUDY : self::REVIEWS_READ;
                    $this->at($returnedAt->copy()->addHours(5), fn () => Review::create([
                        'student_id' => $reader->id,
                        'book_id' => $book->id,
                        'rating' => max(2, min(5, $quality + mt_rand(-1, 1))),
                        'comment' => mt_rand(1, 100) <= 65 ? $pool[mt_rand(0, count($pool) - 1)] : null,
                    ]));
                }
            }
        }

        // --- Keadaan saat ini --------------------------------------------------------
        $loan = fn (Student $s, string $isbn, int $daysAgo) => $this->attempt(
            now()->subDays($daysAgo)->setTime(10, 30),
            fn () => $this->circulation->handOver($this->circulation->requestLoan($s, $books[$isbn])),
        );

        // Budi telat 5 hari mengembalikan Bumi Manusia (denda berjalan).
        $loan($budi, '9789799731234', 12);
        // Rani sudah kembali terlambat 2 hari dan dendanya belum dilunasi.
        $late = $loan($rani, '9780321573513', 20);
        $this->attempt(now()->subDays(11), fn () => $this->circulation->receiveReturn($late));
        // Yoga dan Kevin telat sedikit.
        $loan($readers[3], '9780133594140', 9);
        $loan($readers[15], '9781118063330', 8);

        // Pinjaman yang masih dalam tempo.
        $loan($dewi, '9780134757599', 3);
        $loan($readers[1], '9780735211292', 2);
        $loan($readers[2], '9781449373320', 4);
        $loan($readers[4], '9789799105158', 1);
        $loan($readers[6], '9780465050659', 5);
        $loan($readers[11], '9780262035613', 6);
        $loan($readers[13], '9780307887894', 2);

        // Antrean: Refactoring dan Atomic Habits (eksemplar satu-satunya sedang dipinjam).
        foreach ([[$rani, '9780134757599', 2], [$readers[5], '9780134757599', 1], [$readers[0], '9780735211292', 1], [$readers[8], '9780735211292', 1], [$readers[10], '9780735211292', 0]] as [$s, $isbn, $daysAgo]) {
            $this->attempt(now()->subDays($daysAgo)->subHours(mt_rand(1, 8)), fn () => $this->circulation->reserve($s, $books[$isbn]));
        }

        // E-book yang sedang dibaca.
        $this->attempt(now()->subDay(), fn () => $this->circulation->borrowEbook($readers[2], $books['9781593279929']));
        $this->attempt(now()->subHours(6), fn () => $this->circulation->borrowEbook($dewi, $books['9781593279509']));

        // Tiket yang menunggu diambil di meja.
        $this->attempt(now()->subHours(2), fn () => $this->circulation->requestLoan($dewi, $books['9781492078005']));
        $this->attempt(now()->subHours(7), fn () => $this->circulation->requestLoan($readers[7], $books['9780596009205']));
        $this->attempt(now()->subHours(20), fn () => $this->circulation->requestLoan($readers[9], $books['9780441172719']));

        // Daftar baca Dewi.
        $dewi->favorites()->attach($books->only(['9781449373320', '9780262046305', '9786020312583', '9780451524935', '9780465050659'])->pluck('id'));

        // --- Usulan buku -------------------------------------------------------------
        foreach ([
            [$dewi, 'Laravel: Up & Running', 'Matt Stauffer', "O'Reilly", 'Pemrograman web', 'Referensi Laravel versi terbaru untuk tugas besar Pemrograman Web Lanjut.', 'pending', 2],
            [$readers[1], 'System Design Interview', 'Alex Xu', 'Independently published', 'Arsitektur sistem', 'Banyak yang persiapan wawancara magang dan buku ini sering direkomendasikan.', 'approved', 9],
            [$readers[4], 'Gadis Kretek', 'Ratih Kumala', 'Gramedia Pustaka Utama', 'Sastra Indonesia', 'Dipakai di mata kuliah Kajian Prosa Indonesia semester ini.', 'available', 30],
            [$readers[6], 'Refactoring UI', 'Adam Wathan, Steve Schoger', 'Self-published', 'Desain antarmuka', 'Membantu anak DKV dan informatika yang mengerjakan proyek aplikasi bersama.', 'pending', 1],
            [$readers[13], 'The Psychology of Money', 'Morgan Housel', 'Harriman House', 'Keuangan', 'Bacaan pengantar keuangan pribadi untuk UKM investasi kampus.', 'rejected', 21],
        ] as [$s, $title, $author, $publisher, $category, $reason, $status, $daysAgo]) {
            $this->at(now()->subDays($daysAgo), fn () => BookRequest::create([
                'student_id' => $s->id, 'title' => $title, 'author' => $author, 'publisher' => $publisher,
                'category' => $category, 'reason' => $reason, 'status' => $status,
            ]));
        }

        // --- KTM yang menunggu diperiksa admin -----------------------------------------
        foreach ([['1462300231', 'Lia Rahmawati', 'Farmasi', 'P'], ['1462300248', 'Muhammad Arif', 'Teknik Sipil', 'L'], ['1462300256', 'Nurul Hidayah', 'Pendidikan Matematika', 'P']] as $i => [$nim, $name, $major, $gender]) {
            $s = $student($nim, $name, $major, $gender, 'pending');
            $this->at(now()->subHours(30 - $i * 9), fn () => $s->update(['ktm_image' => $this->fakeKtm($s)]));
        }
    }

    /** Jalankan satu langkah sirkulasi di waktu tertentu; langkah yang melanggar aturan dilewati. */
    private function attempt(Carbon $time, callable $step): mixed
    {
        try {
            return $this->at($time, $step);
        } catch (CirculationException) {
            return null;
        }
    }

    private function at(Carbon $time, callable $callback): mixed
    {
        Carbon::setTestNow($time);
        try {
            return $callback();
        } finally {
            Carbon::setTestNow();
        }
    }

    /** Gambar KTM tiruan bertanda "CONTOH", disimpan lewat Media seperti unggahan sungguhan. */
    private function fakeKtm(Student $s): ?string
    {
        if (! function_exists('imagecreatetruecolor')) {
            return null;
        }

        $img = imagecreatetruecolor(640, 400);
        $bg = imagecolorallocate($img, 236, 241, 250);
        $band = imagecolorallocate($img, 35, 64, 122);
        $ink = imagecolorallocate($img, 17, 24, 39);
        $muted = imagecolorallocate($img, 94, 101, 115);
        $red = imagecolorallocate($img, 180, 35, 24);
        imagefill($img, 0, 0, $bg);
        imagefilledrectangle($img, 0, 0, 640, 70, $band);
        imagestring($img, 5, 24, 26, 'KARTU TANDA MAHASISWA', imagecolorallocate($img, 255, 255, 255));
        imagefilledrectangle($img, 24, 100, 164, 280, imagecolorallocate($img, 201, 212, 234));
        foreach ([['NAMA', $s->name, 110], ['NIM', $s->nim, 170], ['PROGRAM STUDI', $s->jurusan, 230]] as [$label, $value, $y]) {
            imagestring($img, 2, 190, $y, $label, $muted);
            imagestring($img, 5, 190, $y + 16, $value, $ink);
        }
        imagestring($img, 5, 420, 340, 'CONTOH / DEMO', $red);

        $path = tempnam(sys_get_temp_dir(), 'ktm').'.png';
        imagepng($img, $path);

        return Media::store(new UploadedFile($path, 'ktm.png', 'image/png', null, true), 'ktm', private: true);
    }
}
