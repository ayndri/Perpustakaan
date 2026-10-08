# PerpusKampus

**Layanan peminjaman perpustakaan kampus.** Mahasiswa memesan buku dari HP dan mendapat tiket QR.
Petugas memindai QR di meja untuk menyerahkan buku. Kalau semua eksemplar sedang dipinjam,
mahasiswa masuk antrean, dan eksemplar yang kembali otomatis jadi tiket untuk orang terdepan.

> _A campus library circulation app. Students reserve a copy and get a QR ticket; the desk scans it to
> hand the book over. Copies that come back go to the head of the waiting queue before they reach the
> shelf. Late returns accrue a daily fine. Laravel 12 on Postgres (Neon), deployed as a single serverless
> function on Vercel. UI and content are in Indonesian._

---

## Kenapa ini bukan CRUD peminjaman

Versi pertama aplikasi ini berbentuk umum: cek `stock > 0`, lalu kurangi stok. Dua mahasiswa yang
menekan "Pinjam" untuk eksemplar terakhir di detik yang sama sama-sama lolos pengecekan, dan stoknya
jadi −1. Versi ini dibangun ulang di sekitar satu aturan: **semua perubahan stok lewat satu kelas,
[`Circulation`](app/Services/Circulation.php), dan setiap operasinya mengunci baris buku dulu**
(`SELECT … FOR UPDATE` di dalam transaksi).

Buktinya ada di [`RaceTest`](tests/Feature/RaceTest.php). Sepuluh proses PHP terpisah memesan satu
eksemplar terakhir pada detik yang sama, di Postgres sungguhan:

| Kondisi | Hasil |
|---|---|
| Dengan lock | 1 berhasil, 9 ditolak dengan pesan "eksemplar habis" |
| Lock dimatikan | 1 berhasil, 9 gagal dengan **exception dari CHECK constraint** `stock >= 0` (di web: halaman error 500) |

Baris kedua memperlihatkan lapisan pengaman kedua. Kalau suatu hari ada jalur kode yang lupa
mengunci, database menolak stok minus alih-alih diam-diam menyimpannya.

Aturan lain yang ikut dari keputusan itu:

- **Eksemplar yang kembali tidak langsung masuk rak.** Kalau ada antrean, eksemplar itu jadi tiket
  untuk orang terdepan (batas ambil 48 jam) dan orangnya dikabari. Yang sama berlaku saat admin
  menambah stok: eksemplar baru melayani antrean dulu.
- **Tiket yang tidak diambil hangus sendiri, tanpa cron.** Vercel Hobby hanya mengizinkan cron harian,
  sedangkan tiket bisa kedaluwarsa kapan saja. Jadi middleware [`SweepCirculation`](app/Http/Middleware/SweepCirculation.php)
  melepas tiket kedaluwarsa di setiap request, sebelum halaman membaca stok. Kalau tidak ada yang
  kedaluwarsa, biayanya satu query ber-index.
- **Denda dihitung, bukan disimpan, selama buku masih dipinjam.** Saat buku kembali, angkanya dikunci
  ke `fine_amount`. Selama ada denda belum lunas atau buku yang telat, peminjaman baru ditolak.
- **Jatuh tempo dan tanggal kembali adalah dua kolom berbeda.** Versi lama menimpa satu kolom
  `return_date` untuk keduanya, sehingga denda mustahil dihitung.

## Fitur

**Mahasiswa**
- Katalog dengan pencarian judul, penulis, dan ISBN, filter kategori dan ketersediaan.
- Pinjam buku fisik, lalu dapat tiket QR. Ambil di meja dalam 24 jam.
- Antrean dengan posisi yang terlihat ("kamu urutan ke-2").
- Perpanjangan sekali selama belum telat dan tidak ada yang antre.
- E-book dengan kuota baca bersamaan. Tautannya hanya dibuka lewat aplikasi, dan selesai otomatis setelah 3 hari.
- Denda berjalan terlihat di profil. Ulasan dibuka setelah buku dikembalikan.
- Verifikasi KTM, daftar baca, dan usulan pengadaan buku.

**Petugas**
- **Meja layanan**: pindai QR tiket atau kartu anggota dengan kamera atau pemindai USB. Satu kartu
  hasil berisi satu aksi yang relevan: serahkan, terima kembali, atau catat denda lunas.
- **Hari ini**: tiket yang harus disiapkan dari rak (diurutkan dari yang paling cepat hangus), buku
  telat beserta dendanya, dan pekerjaan yang menunggu.
- Kelola koleksi, anggota, antrean, verifikasi KTM, dan usulan buku.
- Laporan PDF (peminjaman per rentang tanggal, daftar anggota) dan kartu anggota dengan QR.

## Teknologi

- **Laravel 12** (PHP 8.4), Blade, Tailwind CSS v4.
- **Postgres** di [Neon](https://neon.com).
- **Vercel** lewat runtime komunitas [`vercel-php`](https://github.com/vercel-community/php). Seluruh
  aplikasi jalan sebagai satu fungsi ([`api/index.php`](api/index.php)), region `sin1` supaya dekat
  dengan database di Singapura.
- **Cloudinary** untuk gambar, karena filesystem Vercel read-only. Tanpa `CLOUDINARY_URL`, aplikasi
  memakai disk lokal. Foto KTM disimpan sebagai aset privat dan dibuka lewat URL bertanda tangan
  yang kedaluwarsa dalam 5 menit, hanya dari panel admin ([`Media`](app/Support/Media.php)).
- QR dibuat sebagai SVG murni ([`bacon/bacon-qr-code`](https://github.com/Bacon/BaconQrCode)), karena
  runtime Vercel tidak membawa ekstensi GD. Pemindai di meja memakai `html5-qrcode`.
- PDF dengan `barryvdh/laravel-dompdf`.

Cover buku contoh diambil dari [Open Library](https://openlibrary.org) berdasarkan ISBN asli.
E-book contoh hanya dipasang pada judul yang memang bebas dibaca: *SICP* (MIT Press),
*Eloquent JavaScript* (dari penulisnya), dan *Pride and Prejudice* (Project Gutenberg).

## Menjalankan di lokal

Butuh PHP 8.2+ dengan `pdo_pgsql`, Composer, Node (hanya untuk build CSS), dan database Postgres.

```bash
composer install
npm install && npm run build      # menghasilkan public/css/app.css
cp .env.example .env              # isi DB_* dengan database Postgres-mu
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

Akun demo dari seeder:

| Peran | Email | Password |
|---|---|---|
| Mahasiswa | `dewi@mhs.test` | `password123` |
| Petugas (`/admin`) | `admin@perpus.test` | `password123` |

Seeder mengisi 63 judul nyata ([`database/seeders/data/books.php`](database/seeders/data/books.php),
sampul dari Open Library yang sudah dicek satu per satu), 23 anggota fiktif, sekitar lima bulan
riwayat pinjam beserta ulasan, dan keadaan yang langsung bisa dicoba: pinjaman telat dengan denda
berjalan, denda yang belum lunas, dua buku dengan antrean, tiket yang menunggu diambil, e-book yang
sedang dibaca, usulan buku, dan tiga KTM contoh yang menunggu verifikasi. Semua riwayat dibuat lewat
`Circulation` dengan jam yang diputar mundur, jadi stok, status, dan denda konsisten dengan aturan aplikasi.

## Tes

```bash
php artisan test
```

Tes memakai Postgres, bukan SQLite, karena lock baris dan CHECK constraint termasuk yang diuji.
`phpunit.xml` mengarah ke database `perpus_test`, yang harus dibuat dulu di server yang sama.
CI menjalankan tes yang sama dengan service Postgres ([`.github/workflows/test.yml`](.github/workflows/test.yml)).

## Deploy ke Vercel

1. Import repo di Vercel. `vercel.json` sudah mengatur runtime, rute, dan env yang tidak rahasia.
2. Isi env rahasia: `APP_KEY`, `APP_URL`, `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`, `CLOUDINARY_URL`.
3. Jalankan migrasi dari lokal ke database produksi: `php artisan migrate --seed`.

CSS di-build di lokal dan ikut di-commit (`public/css/app.css`), jadi Vercel tidak perlu menjalankan Node.

## Struktur yang perlu dibaca dulu

| Berkas | Isi |
|---|---|
| [`app/Services/Circulation.php`](app/Services/Circulation.php) | Semua aturan sirkulasi dan penguncian |
| [`config/library.php`](config/library.php) | Angka aturan: lama pinjam, denda, batas ambil |
| [`app/Http/Controllers/Admin/DeskController.php`](app/Http/Controllers/Admin/DeskController.php) | Meja layanan: kode yang dipindai jadi aksi |
| [`tests/Feature/CirculationTest.php`](tests/Feature/CirculationTest.php) | Skenario antrean, denda, perpanjangan, kedaluwarsa |
| [`resources/css/app.css`](resources/css/app.css) | Sistem desain beserta alasan warnanya |
