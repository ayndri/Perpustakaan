<x-layouts.admin title="Antrean">
    <div class="max-w-5xl">
        <h1 class="text-3xl font-semibold">Antrean</h1>
        <p class="mt-1 max-w-2xl text-ink-2">Antrean berjalan sendiri: begitu eksemplar diterima kembali, sistem membuat tiket untuk orang terdepan dan mengabarinya. Halaman ini untuk memantau, dan mengeluarkan seseorang kalau perlu.</p>

        @forelse ($books as $book)
            <section class="card mt-5">
                <div class="flex flex-wrap items-center gap-4 border-b border-line px-5 py-3">
                    <x-book-cover :book="$book" :width="80" class="w-9 shrink-0" />
                    <div class="min-w-0 flex-1">
                        <h2 class="text-lg font-semibold leading-snug">{{ $book->title }}</h2>
                        <p class="text-xs text-muted">{{ $book->on_loan }} eksemplar sedang dipinjam · {{ $book->stock }} di rak</p>
                    </div>
                    <span class="badge-brass">{{ $book->waitingReservations->count() }} antre</span>
                </div>
                <ol class="divide-y divide-line">
                    @foreach ($book->waitingReservations as $i => $reservation)
                        <li class="flex items-center gap-4 px-5 py-3 text-sm">
                            <span class="w-6 text-lg text-muted">{{ $i + 1 }}</span>
                            <div class="min-w-0 flex-1">
                                <a href="{{ route('admin.students.show', $reservation->student) }}" class="font-medium text-ink">{{ $reservation->student->name }}</a>
                                <span class="block text-xs text-muted">sejak {{ tanggal($reservation->created_at, true) }}</span>
                            </div>
                            <form method="POST" action="{{ route('admin.reservations.cancel', $reservation) }}"
                                  onsubmit="return confirm('Keluarkan {{ addslashes($reservation->student->name) }} dari antrean?')">
                                @csrf
                                <button class="btn-ghost btn-sm">Keluarkan</button>
                            </form>
                        </li>
                    @endforeach
                </ol>
            </section>
        @empty
            <x-empty class="mt-6" title="Tidak ada antrean" icon="queue">
                Antrean muncul saat mahasiswa ingin meminjam buku yang semua eksemplarnya sedang keluar.
            </x-empty>
        @endforelse
    </div>
</x-layouts.admin>
