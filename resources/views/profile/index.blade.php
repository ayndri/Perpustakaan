<x-layouts.app title="Pinjamanku">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <h1 class="text-3xl font-semibold">Pinjamanku</h1>
            <p class="mt-1 text-sm text-muted">{{ $student->name }} · <span class="font-mono">{{ $student->nim }}</span> · {{ $student->jurusan }}</p>
        </div>
        <a href="{{ route('profile.edit') }}" class="btn-secondary btn-sm">Pengaturan akun</a>
    </div>

    @if ($notifications->isNotEmpty())
        <div class="mt-6 space-y-2">
            @foreach ($notifications as $note)
                <div class="flex items-start justify-between gap-3 rounded-md border border-brass/25 bg-brass-tint px-4 py-3 text-sm text-brass">
                    <p class="flex items-start gap-2"><x-icon name="info" :size="16" class="mt-0.5" /> {{ $note->data['message'] ?? '' }}</p>
                    <form method="POST" action="{{ route('notifications.read', $note->id) }}">
                        @csrf
                        <button class="text-xs font-semibold underline">Tandai dibaca</button>
                    </form>
                </div>
            @endforeach
        </div>
    @endif

    @if ($outstandingFine > 0)
        <div class="mt-6 flex flex-wrap items-center justify-between gap-3 rounded-md border border-danger/25 bg-danger-tint px-5 py-4 text-danger">
            <div>
                <p class="font-semibold">Denda {{ rupiah($outstandingFine) }}</p>
                <p class="text-sm">Selama ada denda atau buku yang telat, kamu belum bisa meminjam lagi. Bayar di meja layanan dengan menunjukkan NIM.</p>
            </div>
            <x-icon name="coin" :size="28" />
        </div>
    @endif

    {{-- Tiket menunggu diambil: paling mendesak karena bisa hangus. --}}
    @if ($tickets->isNotEmpty())
        <section class="mt-8">
            <h2 class="text-xl font-semibold">Siap diambil</h2>
            <div class="mt-3 grid gap-3 sm:grid-cols-2">
                @foreach ($tickets as $ticket)
                    <a href="{{ route('borrowings.show', $ticket) }}" class="card flex items-center gap-4 p-4 no-underline hover:border-brand/40">
                        <x-book-cover :book="$ticket->book" :width="120" class="w-12 shrink-0" />
                        <div class="min-w-0 flex-1">
                            <p class="truncate font-semibold text-ink">{{ $ticket->book->title }}</p>
                            <p class="text-sm text-brass">Ambil sebelum {{ tanggal($ticket->pickup_expires_at, true) }}</p>
                        </div>
                        <x-icon name="qr" :size="22" class="text-brand" />
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    <section class="mt-8">
        <h2 class="text-xl font-semibold">Sedang dipinjam</h2>
        @forelse ($active as $loan)
            <div class="card mt-3 flex flex-col gap-4 p-4 sm:flex-row sm:items-center">
                <a href="{{ route('books.show', $loan->book) }}" class="flex min-w-0 flex-1 items-center gap-4 no-underline">
                    <x-book-cover :book="$loan->book" :width="120" class="w-12 shrink-0" />
                    <div class="min-w-0">
                        <p class="truncate font-semibold text-ink">{{ $loan->book->title }}</p>
                        <div class="mt-1 flex flex-wrap items-center gap-2 text-sm">
                            <x-loan-status :loan="$loan" />
                            @if ($loan->type === 'online')
                                <span class="text-muted">akses sampai {{ tanggal($loan->due_at, true) }}</span>
                            @elseif ($loan->isOverdue())
                                <span class="text-danger">denda berjalan {{ rupiah($loan->currentFine()) }}</span>
                            @else
                                <span class="text-brass">kembali sebelum {{ tanggal($loan->due_at) }}</span>
                            @endif
                        </div>
                    </div>
                </a>
                <div class="flex flex-wrap gap-2 sm:justify-end">
                    @if ($loan->type === 'online')
                        <a href="{{ route('borrowings.read', $loan) }}" target="_blank" rel="noopener" class="btn-primary btn-sm">Baca</a>
                        <form method="POST" action="{{ route('borrowings.return-ebook', $loan) }}">
                            @csrf
                            <button class="btn-secondary btn-sm">Selesai baca</button>
                        </form>
                    @elseif (! $loan->isOverdue() && $loan->renewals < config('library.max_renewals'))
                        @if (($queueLengths[$loan->book_id] ?? 0) > 0)
                            <span class="text-xs text-muted">Tidak bisa diperpanjang: ada yang antre</span>
                        @else
                            <form method="POST" action="{{ route('borrowings.renew', $loan) }}">
                                @csrf
                                <button class="btn-secondary btn-sm"><x-icon name="renew" :size="14" /> Perpanjang {{ config('library.renew_days') }} hari</button>
                            </form>
                        @endif
                    @elseif ($loan->renewals > 0 && ! $loan->isOverdue())
                        <span class="text-xs text-muted">Sudah diperpanjang</span>
                    @endif
                </div>
            </div>
        @empty
            <x-empty class="mt-3" title="Tidak ada buku yang sedang kamu pinjam" icon="book">
                <x-slot:action><a href="{{ route('books.index') }}" class="btn-secondary">Buka katalog</a></x-slot:action>
            </x-empty>
        @endforelse
    </section>

    @if ($reservations->isNotEmpty())
        <section class="mt-8">
            <h2 class="text-xl font-semibold">Antreanku</h2>
            <ul class="mt-3 divide-y divide-line rounded-md border border-line bg-surface">
                @foreach ($reservations as $reservation)
                    <li class="flex items-center justify-between gap-4 px-4 py-3">
                        <a href="{{ route('books.show', $reservation->book) }}" class="min-w-0 no-underline">
                            <p class="truncate font-semibold text-ink">{{ $reservation->book->title }}</p>
                            <p class="text-sm text-muted">Urutan ke-{{ $reservation->position() }}, sejak {{ tanggal($reservation->created_at) }}</p>
                        </a>
                        <form method="POST" action="{{ route('reservations.cancel', $reservation) }}">
                            @csrf
                            <button class="btn-ghost btn-sm">Keluar</button>
                        </form>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif

    <section class="mt-10">
        <h2 class="text-xl font-semibold">Riwayat</h2>
        @if ($history->isEmpty())
            <p class="mt-3 text-sm text-muted">Riwayat peminjaman akan muncul di sini setelah buku pertamamu dikembalikan.</p>
        @else
            <div class="table-wrap mt-3">
                <table class="table">
                    <thead><tr><th>Buku</th><th>Diambil</th><th>Kembali</th><th>Status</th><th class="text-right">Denda</th></tr></thead>
                    <tbody>
                        @foreach ($history as $loan)
                            <tr>
                                <td><a href="{{ route('books.show', $loan->book) }}" class="font-medium text-ink">{{ $loan->book->title }}</a></td>
                                <td class="whitespace-nowrap text-muted">{{ tanggal($loan->handed_over_at) }}</td>
                                <td class="whitespace-nowrap text-muted">{{ tanggal($loan->returned_at) }}</td>
                                <td><x-loan-status :loan="$loan" /></td>
                                <td class="whitespace-nowrap text-right">{{ $loan->fine_amount ? rupiah($loan->fine_amount).($loan->fine_paid_at ? ' (lunas)' : '') : '–' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>

    @if ($requests->isNotEmpty())
        <section class="mt-10">
            <div class="flex items-baseline justify-between">
                <h2 class="text-xl font-semibold">Usulan terakhir</h2>
                <a href="{{ route('student.requests.index') }}" class="text-sm font-semibold text-brand">Semua usulan</a>
            </div>
            <ul class="mt-3 space-y-2 text-sm">
                @foreach ($requests as $request)
                    <li class="flex items-center justify-between gap-3">
                        <span>{{ $request->title }} <span class="text-muted">· {{ $request->author }}</span></span>
                        @include('requests._status', ['status' => $request->status])
                    </li>
                @endforeach
            </ul>
        </section>
    @endif
</x-layouts.app>
