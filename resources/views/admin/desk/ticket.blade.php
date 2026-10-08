<x-layouts.admin title="Tiket {{ $ticket->ticket_number }}">
    <div class="max-w-2xl">
        <a href="{{ route('admin.desk') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-muted no-underline hover:text-ink">
            <x-icon name="arrow-left" :size="16" /> Pindai kode lain
        </a>

        <div class="card mt-4 overflow-hidden">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-line bg-paper-2/60 px-5 py-3">
                <span class="font-mono text-lg font-semibold tracking-wider">{{ $ticket->ticket_number }}</span>
                <x-loan-status :loan="$ticket" />
            </div>

            <div class="grid gap-5 p-5 sm:grid-cols-[6rem_1fr]">
                <x-book-cover :book="$ticket->book" :width="200" class="w-24" />
                <div>
                    <h1 class="text-2xl font-semibold leading-snug">{{ $ticket->book->title }}</h1>
                    <p class="text-ink-2">{{ $ticket->book->author }}</p>
                    @if ($ticket->book->shelfLabel())
                        <p class="mt-2 text-sm text-ink-2">Ambil dari rak <span class="spine">{{ $ticket->book->shelfLabel() }}</span></p>
                    @endif

                    <dl class="mt-4 grid grid-cols-2 gap-x-6 gap-y-3 border-t border-line pt-4 text-sm">
                        <div>
                            <dt class="text-muted">Peminjam</dt>
                            <dd><a href="{{ route('admin.desk.lookup', ['kode' => $ticket->student->nim]) }}" class="font-semibold text-ink">{{ $ticket->student->name }}</a></dd>
                            <dd class="font-mono text-xs text-muted">{{ $ticket->student->nim }}</dd>
                        </div>
                        @if ($ticket->status === 'pending')
                            <div><dt class="text-muted">Batas ambil</dt><dd class="font-medium">{{ tanggal($ticket->pickup_expires_at, true) }}</dd></div>
                        @elseif ($ticket->status === 'active')
                            <div><dt class="text-muted">Jatuh tempo</dt><dd class="font-medium {{ $ticket->isOverdue() ? 'text-danger' : '' }}">{{ tanggal($ticket->due_at) }}</dd></div>
                            <div><dt class="text-muted">Diserahkan</dt><dd>{{ tanggal($ticket->handed_over_at, true) }}</dd></div>
                            @if ($ticket->isOverdue())
                                <div><dt class="text-muted">Denda jika kembali sekarang</dt><dd class="font-semibold text-danger">{{ rupiah($ticket->currentFine()) }}</dd></div>
                            @endif
                        @elseif ($ticket->status === 'returned')
                            <div><dt class="text-muted">Dikembalikan</dt><dd>{{ tanggal($ticket->returned_at, true) }}</dd></div>
                            @if ($ticket->fine_amount)
                                <div><dt class="text-muted">Denda</dt><dd class="font-semibold {{ $ticket->fine_paid_at ? '' : 'text-danger' }}">{{ rupiah($ticket->fine_amount) }} {{ $ticket->fine_paid_at ? '(lunas)' : '' }}</dd></div>
                            @endif
                        @endif
                    </dl>
                </div>
            </div>

            {{-- Satu aksi utama sesuai status, supaya petugas tidak salah tekan. --}}
            <div class="flex flex-wrap items-center gap-2 border-t border-line bg-paper-2/40 px-5 py-4">
                @if ($ticket->status === 'pending')
                    @unless ($ticket->student->isVerified())
                        <p class="w-full text-sm text-danger">Akun peminjam belum terverifikasi.</p>
                    @endunless
                    <form method="POST" action="{{ route('admin.borrowings.hand-over', $ticket) }}">
                        @csrf
                        <button class="btn-primary"><x-icon name="check" :size="16" /> Serahkan buku</button>
                    </form>
                    <form method="POST" action="{{ route('admin.borrowings.reject', $ticket) }}" onsubmit="return confirm('Tolak tiket ini? Eksemplar akan dilepas.')">
                        @csrf
                        <button class="btn-danger">Tolak</button>
                    </form>
                    <p class="text-xs text-muted sm:ml-auto">Jatuh tempo jadi {{ tanggal(now()->addDays(config('library.loan_days'))) }}</p>
                @elseif ($ticket->status === 'active' && $ticket->type === 'offline')
                    <form method="POST" action="{{ route('admin.borrowings.receive', $ticket) }}">
                        @csrf
                        <button class="btn-primary"><x-icon name="download" :size="16" /> Terima kembali</button>
                    </form>
                    @if ($ticket->isOverdue())
                        <p class="text-sm text-danger">Denda {{ rupiah($ticket->currentFine()) }} akan dicatat.</p>
                    @endif
                @elseif ($ticket->hasUnpaidFine())
                    <form method="POST" action="{{ route('admin.borrowings.pay-fine', $ticket) }}">
                        @csrf
                        <button class="btn-primary"><x-icon name="coin" :size="16" /> Catat denda {{ rupiah($ticket->fine_amount) }} lunas</button>
                    </form>
                @else
                    <p class="text-sm text-muted">Tidak ada tindakan untuk tiket ini.</p>
                @endif
            </div>
        </div>
    </div>
</x-layouts.admin>
