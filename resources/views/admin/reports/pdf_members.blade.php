<!DOCTYPE html>
<html>

<head>
    <title>Laporan Data Anggota</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 10pt;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h2 {
            margin: 0;
            text-transform: uppercase;
        }

        .header p {
            margin: 5px 0;
            color: #555;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #444;
            padding: 6px 8px;
            text-align: left;
            vertical-align: middle;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
        }

        /* Styling khusus kolom */
        .col-no {
            width: 5%;
            text-align: center;
        }

        .col-nim {
            width: 15%;
            text-align: center;
        }

        .col-jurusan {
            width: 20%;
        }

        .col-status {
            width: 10%;
            text-align: center;
        }

        .badge-verified {
            color: green;
            font-weight: bold;
        }

        .badge-unverified {
            color: red;
            font-style: italic;
        }

        .footer {
            margin-top: 40px;
            text-align: right;
            page-break-inside: avoid;
        }
    </style>
</head>

<body>

    <div class="header">
        <h2>PERPUSTAKAAN KAMPUS</h2>
        <p>Jl. Pendidikan No. 123, Kota Coding, Indonesia</p>
        <hr>
        <h3>LAPORAN DATA ANGGOTA</h3>
        @if($jurusan && $jurusan != 'Semua')
        <p>Filter Jurusan: <strong>{{ $jurusan }}</strong></p>
        @else
        <p>Kategori: Semua Jurusan</p>
        @endif
        <p style="font-size: 9pt;">Dicetak pada: {{ date('d M Y, H:i') }} WIB</p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="col-no">No</th>
                <th class="col-nim">NIM / NBI</th>
                <th>Nama Lengkap</th>
                <th>Email</th>
                <th class="col-jurusan">Jurusan</th>
                <th class="col-status">Status</th>
                <th>Tgl Daftar</th>
            </tr>
        </thead>
        <tbody>
            @foreach($students as $index => $s)
            <tr>
                <td class="col-no">{{ $index + 1 }}</td>
                <td class="col-nim" style="font-family: monospace; font-size: 11pt;">{{ $s->nim }}</td>
                <td>
                    {{ strtoupper($s->name) }}
                </td>
                <td>{{ $s->email }}</td>
                <td>{{ $s->jurusan }}</td>
                <td class="col-status">
                    @if($s->verification_status == 'verified')
                    <span class="badge-verified">Aktif</span>
                    @else
                    <span class="badge-unverified">Pending</span>
                    @endif
                </td>
                <td>{{ $s->created_at->format('d/m/Y') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Mengetahui,</p>
        <br><br><br>
        <p>( _______________________ )<br>Kepala Administrasi</p>
    </div>

</body>

</html>