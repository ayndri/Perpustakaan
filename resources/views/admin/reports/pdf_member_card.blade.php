<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Kartu anggota {{ $student->nim }}</title>
    {{-- Ukuran kartu ID-1 (85,6 × 54 mm). dompdf tidak mendukung flex/grid, jadi tata letak pakai tabel. --}}
    <style>
        @page { margin: 0; }
        body { margin: 0; font-family: 'DejaVu Sans', sans-serif; color: #1d2622; background: #fffdf8; }
        .band { background: #1f4d3a; color: #fff; padding: 7px 10px; }
        .band .name { font-family: 'DejaVu Serif', serif; font-size: 9pt; font-weight: bold; }
        .band .sub { font-size: 5.5pt; color: #c9d8ce; }
        .body { padding: 8px 10px 0; }
        .label { font-size: 5pt; color: #5f6963; text-transform: uppercase; letter-spacing: .4px; }
        .value { font-size: 7.5pt; font-weight: bold; margin-bottom: 4px; }
        .nim { font-family: 'DejaVu Sans Mono', monospace; font-size: 8.5pt; letter-spacing: .5px; }
        .foot { position: absolute; bottom: 5px; left: 10px; font-size: 4.8pt; color: #5f6963; }
        td { vertical-align: top; padding: 0; }
    </style>
</head>
<body>
    <div class="band">
        <div class="name">PerpusKampus</div>
        <div class="sub">KARTU ANGGOTA PERPUSTAKAAN</div>
    </div>
    <div class="body">
        <table width="100%" cellspacing="0" cellpadding="0">
            <tr>
                <td>
                    <div class="label">Nama</div>
                    <div class="value">{{ \Illuminate\Support\Str::limit($student->name, 26) }}</div>
                    <div class="label">NIM</div>
                    <div class="value nim">{{ $student->nim }}</div>
                    <div class="label">Program studi</div>
                    <div class="value">{{ \Illuminate\Support\Str::limit($student->jurusan, 26) }}</div>
                </td>
                <td width="62" align="right">
                    <img src="{{ \App\Support\Qr::dataUri($student->nim, 120) }}" width="58" height="58" alt="QR NIM">
                </td>
            </tr>
        </table>
    </div>
    <div class="foot">Tunjukkan kartu ini di meja layanan untuk pengembalian buku dan pembayaran denda.</div>
</body>
</html>
