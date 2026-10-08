<x-layouts.app title="Tiket {{ $borrowing->ticket_number }}">
    <a href="{{ route('profile') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-muted no-underline hover:text-ink">
        <x-icon name="arrow-left" :size="16" /> Pinjamanku
    </a>

    <div class="mx-auto mt-6 max-w-md">
        <div class="slip overflow-hidden">
            <div class="flex items-center justify-between border-b border-line bg-brand px-5 py-3 text-white">
                <span class="font-semibold">PerpusKampus</span>
                <span class="text-xs text-brand-soft">Tiket peminjaman</span>
            </div>

            <div class="px-6 pb-6 pt-5">
                <div class="flex gap-4">
                    <x-book-cover :book="$borrowing->book" :width="160" class="w-16 shrink-0" />
                    <div class="min-w-0">
                        <h1 class="text-xl font-semibold leading-snug">{{ $borrowing->book->title }}</h1>
                        <p class="text-sm text-muted">{{ $borrowing->book->author }}</p>
                        @if ($borrowing->book->shelfLabel())
                            <p class="mt-2"><span class="spine">{{ $borrowing->book->shelfLabel() }}</span></p>
                        @endif
                    </div>
                </div>

                @if ($borrowing->status === 'pending')
                    <div class="mt-6 flex flex-col items-center">
                        <x-qr :value="$borrowing->ticket_number" :size="220" class="border border-line" />
                        <p class="mt-3 font-mono text-2xl font-semibold tracking-[0.15em]">{{ $borrowing->ticket_number }}</p>
                        <p class="text-xs text-muted">Tunjukkan QR ini, atau sebutkan kodenya, di meja layanan.</p>
                    </div>

                    <div class="mt-6 flex items-center justify-between gap-4 rounded-md bg-brass-tint px-4 py-3">
                        <div class="text-sm text-brass">
                            <p class="font-semibold">Ambil sebelum</p>
                            <p>{{ tanggal($borrowing->pickup_expires_at, true) }}</p>
                        </div>
                        <span class="stamp">{{ $borrowing->pickup_expires_at->diffForHumans(['parts' => 1, 'syntax' => \Carbon\CarbonInterface::DIFF_ABSOLUTE]) }} lagi</span>
                    </div>
                    <p class="mt-3 text-xs text-muted">Lewat dari batas itu tiket hangus dan eksemplarnya diberikan ke peminjam lain atau antrean berikutnya.</p>

                    <form method="POST" action="{{ route('borrowings.cancel', $borrowing) }}" class="mt-5"
                          onsubmit="return confirm('Batalkan tiket ini? Eksemplarnya akan dilepas.')">
                        @csrf
                        <button class="btn-ghost btn-sm text-danger hover:bg-danger-tint">Batalkan tiket</button>
                    </form>
                @elseif ($borrowing->status === 'active')
                    <dl class="mt-6 grid grid-cols-2 gap-4 text-sm">
                        <div><dt class="text-muted">Diambil</dt><dd class="font-medium">{{ tanggal($borrowing->handed_over_at) }}</dd></div>
                        <div><dt class="text-muted">Kembali sebelum</dt><dd><span class="stamp mt-1 text-xs">{{ tanggal($borrowing->due_at) }}</span></dd></div>
                    </dl>
                    <div class="mt-5"><x-loan-status :loan="$borrowing" /></div>
                @else
                    <div class="mt-6 flex items-center gap-3">
                        <x-loan-status :loan="$borrowing" />
                        <span class="font-mono text-sm text-muted">{{ $borrowing->ticket_number }}</span>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>
