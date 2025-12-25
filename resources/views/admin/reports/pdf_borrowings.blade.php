<!DOCTYPE html>
<html>

<head>
    <title>Laporan Peminjaman</title>
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
        }

        .header p {
            margin: 5px 0;
            color: #555;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #333;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .status-badge {
            font-weight: bold;
        }

        .footer {
            margin-top: 50px;
            text-align: right;
        }
    </style>
</head>

<body>

    <div class="header">
        <h2>PERPUSTAKAAN KAMPUS</h2>
        <p>Jl. Pendidikan No. 123, Kota Coding, Indonesia</p>
        <hr>
        <h3>LAPORAN PEMINJAMAN BUKU</h3>
        <p>Periode: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal Pinjam</th>
                <th>Mahasiswa</th>
                <th>Judul Buku</th>
                <th>Tipe</th>
                <th>Status</th>
                <th>Tgl Kembali</th>
            </tr>
        </thead>
        <tbody>
            @foreach($borrowings as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($item->borrow_date)->format('d/m/Y') }}</td>
                <td>
                    {{ $item->student->name }}<br>
                    <small>({{ $item->student->nim }})</small>
                </td>
                <td>{{ $item->book->title }}</td>
                <td>{{ ucfirst($item->type) }}</td>
                <td>
                    @if($item->status == 'active') Sedang Dipinjam
                    @elseif($item->status == 'returned') Selesai
                    @elseif($item->status == 'pending') Menunggu
                    @else {{ ucfirst($item->status) }}
                    @endif
                </td>
                <td>
                    @if($item->return_date_actual)
                    {{ \Carbon\Carbon::parse($item->return_date_actual)->format('d/m/Y') }}
                    @else
                    -
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak pada: {{ date('d M Y H:i') }}</p>
        <br><br><br>
        <p>( _______________________ )<br>Kepala Perpustakaan</p>
    </div>

</body>

</html>