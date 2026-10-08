<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan peminjaman</title>
    <style>
        @page { margin: 28px 32px; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 9.5pt; color: #1d2622; }
        h1 { font-size: 15pt; margin: 0; color: #1f4d3a; }
        .meta { color: #5f6963; margin: 4px 0 14px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #efe9dc; text-align: left; padding: 6px 7px; font-size: 8.5pt; border-bottom: 1px solid #cfc3aa; }
        td { padding: 6px 7px; border-bottom: 1px solid #e2d9c6; vertical-align: top; }
        .mono { font-family: 'DejaVu Sans Mono', monospace; font-size: 8.5pt; }
        .right { text-align: right; }
        .late { color: #a3321f; font-weight: bold; }
        .summary { margin-top: 14px; }
        .summary td { border: 0; padding: 2px 0; }
    </style>
</head>
<body>
    <h1>Laporan peminjaman PerpusKampus</h1>
    <p class="meta">
        Buku diserahkan {{ $startDate->translatedFormat('j F Y') }} sampai {{ $endDate->translatedFormat('j F Y') }}
        · dicetak {{ now()->translatedFormat('j F Y, H.i') }}
    </p>

    <table>
        <thead>
            <tr>
                <th>No</th><th>Tiket</th><th>Peminjam</th><th>Buku</th><th>Diserahkan</th><th>Jatuh tempo</th><th>Kembali</th><th>Status</th><th class="right">Denda</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($borrowings as $i => $loan)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td class="mono">{{ $loan->ticket_number }}</td>
                    <td>{{ $loan->student->name }}<br><span class="mono">{{ $loan->student->nim }}</span></td>
                    <td>{{ $loan->book->title }}{{ $loan->type === 'online' ? ' (e-book)' : '' }}</td>
                    <td>{{ tanggal($loan->handed_over_at) }}</td>
                    <td>{{ tanggal($loan->due_at) }}</td>
                    <td>{{ tanggal($loan->returned_at) }}</td>
                    <td class="{{ $loan->isOverdue() ? 'late' : '' }}">
                        {{ match (true) { $loan->isOverdue() => 'Telat '.$loan->daysLate().' hari', $loan->status === 'active' => 'Dipinjam', $loan->status === 'returned' => 'Kembali', default => ucfirst($loan->status) } }}
                    </td>
                    <td class="right">{{ $loan->currentFine() ? rupiah($loan->currentFine()).($loan->fine_paid_at ? ' (lunas)' : '') : '–' }}</td>
                </tr>
            @empty
                <tr><td colspan="9">Tidak ada buku yang diserahkan pada rentang ini.</td></tr>
            @endforelse
        </tbody>
    </table>

    <table class="summary">
        <tr><td>Total peminjaman</td><td class="right">{{ $borrowings->count() }}</td></tr>
        <tr><td>Masih dipinjam</td><td class="right">{{ $borrowings->where('status', 'active')->count() }}</td></tr>
        <tr><td>Denda tercatat</td><td class="right">{{ rupiah($borrowings->sum(fn ($l) => $l->currentFine())) }}</td></tr>
    </table>
</body>
</html>
