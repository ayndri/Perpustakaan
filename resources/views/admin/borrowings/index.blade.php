<x-layouts.admin title="Peminjaman">
    <div class="max-w-6xl">
        <h1 class="text-3xl font-semibold">Peminjaman</h1>

        <div class="mt-5 flex flex-wrap items-center gap-3">
            <nav class="flex flex-wrap gap-1 rounded-md border border-line bg-surface p-1" aria-label="Filter">
                @foreach ($filters as $key => $label)
                    <a href="{{ route('admin.borrowings.index', ['filter' => $key, 'q' => $search ?: null]) }}"
                       class="{{ $filter === $key ? 'nav-link-active' : 'nav-link' }} py-1.5" @if ($filter === $key) aria-current="page" @endif>{{ $label }}</a>
                @endforeach
            </nav>
            <form method="GET" class="ml-auto flex gap-2" role="search">
                <input type="hidden" name="filter" value="{{ $filter }}">
                <label for="q" class="sr-only">Cari</label>
                <input id="q" name="q" value="{{ $search }}" class="input w-64" placeholder="Tiket, nama, NIM, atau judul">
                <button class="btn-secondary">Cari</button>
            </form>
        </div>

        @if ($borrowings->isEmpty())
            <x-empty class="mt-6" title="Tidak ada data di filter ini" icon="inbox">
                {{ $search !== '' ? 'Coba kata kunci lain atau pindah filter.' : 'Data akan muncul di sini begitu ada transaksi dengan status ini.' }}
            </x-empty>
        @else
            <div class="table-wrap mt-5">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Tiket</th>
                            <th>Buku</th>
                            <th>Peminjam</th>
                            <th>{{ match ($filter) { 'pending' => 'Hangus', 'history' => 'Selesai', 'fines' => 'Kembali', default => 'Jatuh tempo' } }}</th>
                            <th>Status</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($borrowings as $loan)
                            <tr>
                                <td class="whitespace-nowrap font-mono text-xs"><a href="{{ route('admin.desk.lookup', ['kode' => $loan->ticket_number]) }}" class="text-ink">{{ $loan->ticket_number }}</a></td>
                                <td class="min-w-48">
                                    <span class="font-medium">{{ $loan->book->title }}</span>
                                    @if ($loan->type === 'online')<span class="badge-neutral ml-1">E-book</span>@endif
                                </td>
                                <td class="min-w-40">
                                    <a href="{{ route('admin.students.show', $loan->student) }}" class="text-ink">{{ $loan->student->name }}</a>
                                    <span class="block font-mono text-xs text-muted">{{ $loan->student->nim }}</span>
                                </td>
                                <td class="whitespace-nowrap text-ink-2">
                                    @switch($filter)
                                        @case('pending') {{ tanggal($loan->pickup_expires_at, true) }} @break
                                        @case('history') {{ tanggal($loan->returned_at ?? $loan->updated_at) }} @break
                                        @case('fines') {{ tanggal($loan->returned_at) }} @break
                                        @default {{ tanggal($loan->due_at) }}
                                    @endswitch
                                </td>
                                <td>
                                    <x-loan-status :loan="$loan" />
                                    @if ($loan->fine_amount || $loan->isOverdue())
                                        <span class="block pt-1 text-xs {{ $loan->fine_paid_at ? 'text-muted' : 'text-danger' }}">{{ rupiah($loan->currentFine()) }}{{ $loan->fine_paid_at ? ' lunas' : '' }}</span>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap text-right">
                                    @if ($loan->status === 'pending')
                                        <form method="POST" action="{{ route('admin.borrowings.hand-over', $loan) }}" class="inline">@csrf<button class="btn-primary btn-sm">Serahkan</button></form>
                                    @elseif ($loan->status === 'active' && $loan->type === 'offline')
                                        <form method="POST" action="{{ route('admin.borrowings.receive', $loan) }}" class="inline">@csrf<button class="btn-secondary btn-sm">Terima kembali</button></form>
                                    @elseif ($loan->hasUnpaidFine())
                                        <form method="POST" action="{{ route('admin.borrowings.pay-fine', $loan) }}" class="inline">@csrf<button class="btn-secondary btn-sm">Catat lunas</button></form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-5">{{ $borrowings->links() }}</div>
        @endif
    </div>
</x-layouts.admin>
