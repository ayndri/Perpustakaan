<x-layouts.admin title="Hari ini">
    <div class="max-w-6xl">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-3xl font-semibold">Hari ini</h1>
                <p class="mt-1 text-sm text-muted">{{ now()->translatedFormat('l, j F Y') }}</p>
            </div>
            <a href="{{ route('admin.desk') }}" class="btn-primary"><x-icon name="scan" :size="16" /> Buka meja layanan</a>
        </div>

        {{-- Antrian pekerjaan lain, masing-masing langsung menuju halamannya. --}}
        <div class="mt-6 grid grid-cols-2 gap-3 lg:grid-cols-4">
            @foreach ([
                ['Verifikasi KTM', $pendingVerifications, 'menunggu diperiksa', route('admin.students.verification'), $pendingVerifications > 0],
                ['Usulan buku', $pendingRequests, 'belum ditinjau', route('admin.requests.index', ['status' => 'pending']), $pendingRequests > 0],
                ['Antrean', $waitingReservations, 'mahasiswa menunggu', route('admin.reservations.index'), false],
                ['Denda belum lunas', rupiah($unpaidFines), 'dari buku yang sudah kembali', route('admin.borrowings.index', ['filter' => 'fines']), $unpaidFines > 0],
            ] as [$label, $value, $caption, $url, $attention])
                <a href="{{ $url }}" class="card block p-4 no-underline hover:border-brand/40">
                    <p class="text-xs font-semibold text-ink-2">{{ $label }}</p>
                    <p class="mt-1 text-2xl font-semibold {{ $attention ? 'text-brass' : 'text-ink' }}">{{ $value }}</p>
                    <p class="text-xs text-muted">{{ $caption }}</p>
                </a>
            @endforeach
        </div>

        <div class="mt-8 grid gap-8 xl:grid-cols-2">
            <section>
                <div class="flex items-baseline justify-between">
                    <h2 class="text-xl font-semibold">Tiket menunggu diambil</h2>
                    <span class="text-sm text-muted">{{ $tickets->count() }}</span>
                </div>
                <p class="text-xs text-muted">Siapkan bukunya dari rak. Diurutkan dari yang paling cepat hangus.</p>
                @if ($tickets->isEmpty())
                    <p class="mt-3 rounded-md border border-dashed border-line-strong px-4 py-6 text-center text-sm text-muted">Tidak ada tiket yang menunggu.</p>
                @else
                    <div class="table-wrap mt-3">
                        <table class="table">
                            <thead><tr><th>Rak</th><th>Buku</th><th>Peminjam</th><th>Hangus</th></tr></thead>
                            <tbody>
                                @foreach ($tickets as $ticket)
                                    <tr>
                                        <td class="whitespace-nowrap"><span class="spine">{{ $ticket->book->shelfLabel() ?? '–' }}</span></td>
                                        <td><a href="{{ route('admin.desk.lookup', ['kode' => $ticket->ticket_number]) }}" class="font-medium text-ink">{{ $ticket->book->title }}</a></td>
                                        <td class="text-ink-2">{{ $ticket->student->name }}</td>
                                        <td class="whitespace-nowrap text-ink-2">{{ $ticket->pickup_expires_at->diffForHumans() }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </section>

            <section>
                <div class="flex items-baseline justify-between">
                    <h2 class="text-xl font-semibold">Lewat jatuh tempo</h2>
                    <span class="text-sm text-muted">{{ $overdue->count() }}</span>
                </div>
                <p class="text-xs text-muted">Denda berjalan {{ rupiah(config('library.fine_per_day')) }} per hari sampai bukunya kembali.</p>
                @if ($overdue->isEmpty())
                    <p class="mt-3 rounded-md border border-dashed border-line-strong px-4 py-6 text-center text-sm text-muted">Semua pinjaman masih dalam tempo.</p>
                @else
                    <div class="table-wrap mt-3">
                        <table class="table">
                            <thead><tr><th>Peminjam</th><th>Buku</th><th>Telat</th><th class="text-right">Denda</th></tr></thead>
                            <tbody>
                                @foreach ($overdue as $loan)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.desk.lookup', ['kode' => $loan->student->nim]) }}" class="font-medium text-ink">{{ $loan->student->name }}</a>
                                            <span class="block font-mono text-xs text-muted">{{ $loan->student->nim }}</span>
                                        </td>
                                        <td class="text-ink-2">{{ $loan->book->title }}</td>
                                        <td class="whitespace-nowrap font-semibold text-danger">{{ $loan->daysLate() }} hari</td>
                                        <td class="whitespace-nowrap text-right">{{ rupiah($loan->currentFine()) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                @if ($dueToday->isNotEmpty())
                    <p class="mt-4 text-sm text-ink-2">
                        <span class="font-semibold text-brass">{{ $dueToday->count() }} buku jatuh tempo hari ini:</span>
                        {{ $dueToday->map(fn ($l) => $l->book->title.' ('.$l->student->name.')')->join(', ') }}.
                    </p>
                @endif
            </section>
        </div>

        <section class="mt-10 border-t border-line pt-8">
            <h2 class="text-xl font-semibold">Buku diserahkan per bulan, {{ now()->year }}</h2>
            <p class="text-xs text-muted">{{ $activeLoans }} pinjaman sedang berjalan saat ini.</p>
            @php($max = max(1, $loansPerMonth->max()))
            <div class="mt-5 flex h-40 items-end gap-1.5 sm:gap-3" role="img" aria-label="Grafik jumlah buku diserahkan per bulan tahun {{ now()->year }}">
                @foreach ($loansPerMonth as $i => $count)
                    <div class="flex flex-1 flex-col items-center gap-1.5">
                        <span class="text-[11px] font-medium text-ink-2">{{ $count ?: '' }}</span>
                        <div class="w-full rounded-t-sm {{ $i + 1 === now()->month ? 'bg-brand' : 'bg-brand-soft' }}" style="height: {{ $count ? max(4, round($count / $max * 112)) : 1 }}px"></div>
                        <span class="text-[11px] text-muted">{{ \Illuminate\Support\Carbon::create(null, $i + 1, 1)->translatedFormat('M') }}</span>
                    </div>
                @endforeach
            </div>
        </section>

        @php($storage = \App\Support\Media::status())
        <p class="mt-10 flex items-start gap-2 border-t border-line pt-4 text-xs {{ $storage['state'] === 'cloudinary' ? 'text-muted' : 'text-danger' }}">
            <x-icon :name="$storage['state'] === 'cloudinary' ? 'check' : 'alert'" :size="14" class="mt-px" />
            <span><span class="font-semibold">Penyimpanan gambar:</span> {{ $storage['detail'] }}</span>
        </p>
    </div>
</x-layouts.admin>
