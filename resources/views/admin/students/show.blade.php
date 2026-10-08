<x-layouts.admin :title="$student->name">
    <div class="max-w-5xl">
        <a href="{{ route('admin.students.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-muted no-underline hover:text-ink">
            <x-icon name="arrow-left" :size="16" /> Anggota
        </a>

        <div class="mt-4 flex flex-wrap items-start justify-between gap-4">
            <div class="flex items-center gap-4">
                @if ($student->photoUrl())
                    <img src="{{ $student->photoUrl() }}" alt="" class="h-14 w-14 rounded-full object-cover">
                @else
                    <span class="grid h-14 w-14 place-items-center rounded-full bg-brand-tint text-xl text-brand">{{ \Illuminate\Support\Str::substr($student->name, 0, 1) }}</span>
                @endif
                <div>
                    <h1 class="text-3xl font-semibold">{{ $student->name }}</h1>
                    <p class="text-sm text-muted"><span class="font-mono">{{ $student->nim }}</span> · {{ $student->jurusan }} · {{ $student->email }}</p>
                </div>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.desk.lookup', ['kode' => $student->nim]) }}" class="btn-primary btn-sm"><x-icon name="scan" :size="14" /> Layani di meja</a>
                <a href="{{ route('admin.students.card', $student) }}" target="_blank" class="btn-secondary btn-sm"><x-icon name="id" :size="14" /> Cetak kartu</a>
                @if ($student->ktm_image)
                    <a href="{{ route('admin.students.ktm', $student) }}" target="_blank" rel="noopener" class="btn-secondary btn-sm">Lihat KTM</a>
                @endif
            </div>
        </div>

        <dl class="mt-6 grid grid-cols-2 gap-3 sm:grid-cols-4">
            <div class="card p-4"><dt class="text-xs text-muted">Status</dt><dd class="mt-1 font-semibold">{{ ['verified' => 'Terverifikasi', 'pending' => 'Menunggu cek KTM', 'rejected' => 'KTM ditolak', 'none' => 'Belum unggah KTM'][$student->verification_status] }}</dd></div>
            <div class="card p-4"><dt class="text-xs text-muted">Sedang dipinjam</dt><dd class="mt-1 text-2xl font-semibold">{{ $loans->where('status', 'active')->count() }}</dd></div>
            <div class="card p-4"><dt class="text-xs text-muted">Total pinjaman</dt><dd class="mt-1 text-2xl font-semibold">{{ $loans->whereNotNull('handed_over_at')->count() }}</dd></div>
            <div class="card p-4"><dt class="text-xs text-muted">Denda belum lunas</dt><dd class="mt-1 text-2xl font-semibold {{ $outstandingFine ? 'text-danger' : '' }}">{{ rupiah($outstandingFine) }}</dd></div>
        </dl>

        <h2 class="mt-8 text-xl font-semibold">Riwayat peminjaman</h2>
        @if ($loans->isEmpty())
            <p class="mt-3 text-sm text-muted">Belum pernah meminjam.</p>
        @else
            <div class="table-wrap mt-3">
                <table class="table">
                    <thead><tr><th>Tiket</th><th>Buku</th><th>Diambil</th><th>Tempo</th><th>Kembali</th><th>Status</th></tr></thead>
                    <tbody>
                        @foreach ($loans as $loan)
                            <tr>
                                <td class="font-mono text-xs"><a href="{{ route('admin.desk.lookup', ['kode' => $loan->ticket_number]) }}" class="text-ink">{{ $loan->ticket_number }}</a></td>
                                <td class="font-medium">{{ $loan->book->title }}</td>
                                <td class="whitespace-nowrap text-ink-2">{{ tanggal($loan->handed_over_at) }}</td>
                                <td class="whitespace-nowrap text-ink-2">{{ tanggal($loan->due_at) }}</td>
                                <td class="whitespace-nowrap text-ink-2">{{ tanggal($loan->returned_at) }}</td>
                                <td>
                                    <x-loan-status :loan="$loan" />
                                    @if ($loan->fine_amount)
                                        <span class="block pt-1 text-xs {{ $loan->fine_paid_at ? 'text-muted' : 'text-danger' }}">{{ rupiah($loan->fine_amount) }}{{ $loan->fine_paid_at ? ' lunas' : '' }}</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</x-layouts.admin>
