<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan anggota</title>
    <style>
        @page { margin: 32px 36px; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 10pt; color: #1d2622; }
        h1 { font-size: 15pt; margin: 0; color: #1f4d3a; }
        .meta { color: #5f6963; margin: 4px 0 14px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #efe9dc; text-align: left; padding: 6px 8px; font-size: 9pt; border-bottom: 1px solid #cfc3aa; }
        td { padding: 6px 8px; border-bottom: 1px solid #e2d9c6; }
        .mono { font-family: 'DejaVu Sans Mono', monospace; }
    </style>
</head>
<body>
    <h1>Daftar anggota PerpusKampus</h1>
    <p class="meta">{{ $jurusan ?: 'Semua program studi' }} · {{ $students->count() }} anggota · dicetak {{ now()->translatedFormat('j F Y, H.i') }}</p>

    <table>
        <thead><tr><th>No</th><th>NIM</th><th>Nama</th><th>Program studi</th><th>L/P</th><th>Status</th></tr></thead>
        <tbody>
            @foreach ($students as $i => $s)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td class="mono">{{ $s->nim }}</td>
                    <td>{{ $s->name }}</td>
                    <td>{{ $s->jurusan }}</td>
                    <td>{{ $s->gender }}</td>
                    <td>{{ ['verified' => 'Terverifikasi', 'pending' => 'Menunggu', 'rejected' => 'Ditolak', 'none' => 'Belum unggah KTM'][$s->verification_status] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
