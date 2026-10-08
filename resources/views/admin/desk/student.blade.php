<x-layouts.admin :title="$student->name">
    <div class="max-w-4xl">
        <a href="{{ route('admin.desk') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-muted no-underline hover:text-ink">
            <x-icon name="arrow-left" :size="16" /> Pindai kode lain
        </a>

        <div class="mt-4 flex flex-wrap items-start justify-between gap-4">
            <div>
                <h1 class="text-3xl font-semibold">{{ $student->name }}</h1>
                <p class="mt-1 text-sm text-muted"><span class="font-mono">{{ $student->nim }}</span> · {{ $student->jurusan }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                @if ($student->isVerified())
                    <span class="badge-brand">Terverifikasi</span>
                @else
                    <span class="badge-brass">Belum terverifikasi</span>
                @endif
                <a href="{{ route('admin.students.show', $student) }}" class="btn-secondary btn-sm">Profil lengkap</a>
            </div>
        </div>

        @if ($unpaid->isNotEmpty())
            <section class="mt-6 rounded-md border border-danger/25 bg-danger-tint p-4">
                <h2 class="font-sans text-sm font-semibold text-danger">Denda belum lunas · {{ rupiah($unpaid->sum('fine_amount')) }}</h2>
                <ul class="mt-2 space-y-2">
                    @foreach ($unpaid as $loan)
                        <li class="flex flex-wrap items-center justify-between gap-3 text-sm">
                            <span class="text-ink">{{ $loan->book->title }} <span class="text-muted">· telat {{ $loan->daysLate() }} hari</span></span>
                            <form method="POST" action="{{ route('admin.borrowings.pay-fine', $loan) }}">
                                @csrf
                                <button class="btn-primary btn-sm">Catat {{ rupiah($loan->fine_amount) }} lunas</button>
                            </form>
                        </li>
                    @endforeach
                </ul>
            </section>
        @endif

        <section class="mt-6">
            <h2 class="text-xl font-semibold">Tiket dan pinjaman aktif</h2>
            @forelse ($loans as $loan)
                <div class="card mt-3 flex flex-col gap-3 p-4 sm:flex-row sm:items-center">
                    <div class="flex min-w-0 flex-1 items-center gap-3">
                        <x-book-cover :book="$loan->book" :width="120" class="w-10 shrink-0" />
                        <div class="min-w-0">
                            <p class="truncate font-semibold">{{ $loan->book->title }}</p>
                            <div class="mt-1 flex flex-wrap items-center gap-2 text-xs text-muted">
                                <x-loan-status :loan="$loan" />
                                <span class="font-mono">{{ $loan->ticket_number }}</span>
                                @if ($loan->status === 'active' && $loan->type === 'offline')
                                    <span>tempo {{ tanggal($loan->due_at) }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        @if ($loan->status === 'pending')
                            <form method="POST" action="{{ route('admin.borrowings.hand-over', $loan) }}">
                                @csrf
                                <button class="btn-primary btn-sm">Serahkan</button>
                            </form>
                        @elseif ($loan->type === 'offline')
                            <form method="POST" action="{{ route('admin.borrowings.receive', $loan) }}">
                                @csrf
                                <button class="btn-primary btn-sm">Terima kembali{{ $loan->isOverdue() ? ' · denda '.rupiah($loan->currentFine()) : '' }}</button>
                            </form>
                        @else
                            <span class="text-xs text-muted">E-book, selesai otomatis</span>
                        @endif
                    </div>
                </div>
            @empty
                <p class="mt-3 text-sm text-muted">Tidak ada tiket atau pinjaman aktif.</p>
            @endforelse
        </section>

        @if ($student->isVerified())
            <section class="card-pad mt-8">
                <h2 class="font-sans text-sm font-semibold">Pinjamkan langsung di meja</h2>
                <p class="mt-1 text-xs text-muted">Untuk mahasiswa yang datang tanpa tiket. Aturan yang sama tetap berlaku: batas harian, denda, dan buku telat.</p>
                <form method="POST" action="{{ route('admin.desk.lend', $student) }}" class="mt-3 flex flex-col gap-2 sm:flex-row">
                    @csrf
                    <label for="book_id" class="sr-only">Buku</label>
                    <select id="book_id" name="book_id" class="input" required>
                        <option value="">Pilih buku yang ada di rak</option>
                        @foreach ($books as $book)
                            <option value="{{ $book->id }}">{{ $book->title }} · {{ $book->author }} ({{ $book->stock }})</option>
                        @endforeach
                    </select>
                    <button class="btn-primary shrink-0">Pinjamkan</button>
                </form>
            </section>
        @endif
    </div>
</x-layouts.admin>
