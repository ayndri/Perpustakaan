<x-layouts.admin title="Meja layanan">
    <div class="max-w-5xl">
        <h1 class="text-3xl font-semibold">Meja layanan</h1>
        <p class="mt-1 text-ink-2">Pindai QR tiket untuk menyerahkan buku, atau QR kartu anggota untuk menerima pengembalian dan denda.</p>

        <div class="mt-6 grid gap-6 lg:grid-cols-[1fr_20rem]">
            <div class="card-pad">
                <form id="lookup" action="{{ route('admin.desk.lookup') }}" method="GET">
                    <label for="kode" class="label">Kode tiket atau NIM</label>
                    <div class="flex gap-2">
                        <input id="kode" name="kode" value="{{ old('kode') }}" class="input font-mono text-base uppercase tracking-wider"
                               placeholder="TK-XXXXXX atau NIM" autocomplete="off" autofocus required>
                        <button class="btn-primary">Cari</button>
                    </div>
                    <p class="hint">Pemindai barcode USB juga bisa dipakai: arahkan ke QR, kodenya terketik dan langsung dicari.</p>
                </form>

                <div class="mt-6 border-t border-line pt-6">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <p class="text-sm font-semibold">Pindai dengan kamera</p>
                        <button type="button" id="scan-toggle" class="btn-secondary btn-sm"><x-icon name="camera" :size="15" /> <span>Nyalakan kamera</span></button>
                    </div>
                    <div id="reader" class="mt-4 hidden overflow-hidden rounded-md border border-line bg-ink"></div>
                    <p id="scan-status" class="mt-2 text-xs text-muted" aria-live="polite"></p>
                </div>
            </div>

            <aside>
                <h2 class="font-sans text-sm font-semibold">Tiket menunggu diambil</h2>
                @if ($tickets->isEmpty())
                    <p class="mt-3 text-sm text-muted">Tidak ada tiket yang menunggu.</p>
                @else
                    <ul class="mt-3 divide-y divide-line rounded-md border border-line bg-surface">
                        @foreach ($tickets as $ticket)
                            <li>
                                <a href="{{ route('admin.desk.lookup', ['kode' => $ticket->ticket_number]) }}" class="block px-4 py-3 no-underline hover:bg-paper-2/60">
                                    <span class="flex items-center justify-between gap-2">
                                        <span class="font-mono text-sm font-semibold text-ink">{{ $ticket->ticket_number }}</span>
                                        <span class="text-xs {{ $ticket->pickup_expires_at->diffInHours(now(), true) < 3 ? 'font-semibold text-danger' : 'text-muted' }}">
                                            hangus {{ $ticket->pickup_expires_at->diffForHumans() }}
                                        </span>
                                    </span>
                                    <span class="mt-0.5 block truncate text-sm text-ink-2">{{ $ticket->student->name }} · {{ $ticket->book->title }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </aside>
        </div>
    </div>

    <x-slot:scripts>
        <script src="https://cdn.jsdelivr.net/npm/html5-qrcode@2.3.8/html5-qrcode.min.js" defer></script>
        <script>
            // Kamera hanya dinyalakan atas permintaan petugas; begitu QR terbaca, kodenya langsung dicari.
            document.addEventListener('DOMContentLoaded', () => {
                const button = document.getElementById('scan-toggle');
                const reader = document.getElementById('reader');
                const status = document.getElementById('scan-status');
                const input = document.getElementById('kode');
                let scanner = null;

                const stop = async () => {
                    if (scanner) { await scanner.stop().catch(() => {}); scanner = null; }
                    reader.classList.add('hidden');
                    button.querySelector('span').textContent = 'Nyalakan kamera';
                };

                button.addEventListener('click', async () => {
                    if (scanner) { await stop(); status.textContent = ''; return; }
                    if (!window.Html5Qrcode) { status.textContent = 'Pemindai belum termuat. Periksa koneksi lalu coba lagi.'; return; }

                    reader.classList.remove('hidden');
                    scanner = new Html5Qrcode('reader');
                    status.textContent = 'Meminta izin kamera…';
                    try {
                        await scanner.start({ facingMode: 'environment' }, { fps: 10, qrbox: 220 }, async (text) => {
                            input.value = text.trim();
                            status.textContent = 'Terbaca: ' + input.value;
                            await stop();
                            document.getElementById('lookup').submit();
                        });
                        button.querySelector('span').textContent = 'Matikan kamera';
                        status.textContent = 'Arahkan QR ke kamera.';
                    } catch (e) {
                        await stop();
                        status.textContent = 'Kamera tidak bisa dibuka (' + (e?.message || e) + '). Ketik kodenya saja.';
                    }
                });
            });
        </script>
    </x-slot:scripts>
</x-layouts.admin>
