<x-layouts.app :title="$book->title" wide>
    <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('books.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-muted no-underline hover:text-ink">
        <x-icon name="arrow-left" :size="16" /> Kembali
    </a>

    <div class="mt-6 grid gap-8 md:grid-cols-[15rem_1fr] lg:grid-cols-[17rem_1fr_19rem] lg:gap-10">
        <div class="mx-auto w-44 md:mx-0 md:w-full">
            <x-book-cover :book="$book" :width="500" class="shadow-md shadow-ink/10" />
        </div>

        <div>
            <p class="text-sm font-semibold text-brand">{{ $book->category->name }}</p>
            <h1 class="mt-1 text-3xl font-semibold leading-tight sm:text-4xl">{{ $book->title }}</h1>
            <p class="mt-2 text-lg text-ink-2">{{ $book->author }}</p>

            @if ($book->average_rating)
                <p class="mt-3 flex items-center gap-1.5 text-sm text-ink-2">
                    <x-icon name="star-fill" :size="16" class="text-brass" />
                    <span class="font-semibold text-ink">{{ number_format($book->average_rating, 1, ',', '') }}</span>
                    <span class="text-muted">dari {{ $book->reviews->count() }} ulasan</span>
                </p>
            @endif

            @if ($book->description)
                <div class="prose-book mt-6 max-w-prose leading-relaxed text-ink-2">
                    <p>{{ $book->description }}</p>
                </div>
            @endif

            <dl class="mt-8 grid max-w-lg grid-cols-2 gap-x-6 gap-y-4 border-t border-line pt-6 text-sm">
                <div><dt class="text-muted">Penerbit</dt><dd class="mt-0.5 font-medium">{{ $book->publisher ?? '–' }}</dd></div>
                <div><dt class="text-muted">Tahun</dt><dd class="mt-0.5 font-medium">{{ $book->year }}</dd></div>
                <div><dt class="text-muted">ISBN</dt><dd class="mt-0.5 font-mono text-[13px]">{{ $book->isbn ?? '–' }}</dd></div>
                <div><dt class="text-muted">Lokasi</dt><dd class="mt-0.5">@if ($book->shelfLabel())<span class="spine">{{ $book->shelfLabel() }}</span>@else – @endif</dd></div>
            </dl>
        </div>

        {{-- Panel pinjam: isinya bergantung pada keadaan mahasiswa terhadap buku ini. --}}
        <aside class="md:col-span-2 lg:col-span-1">
            <div class="card-pad lg:sticky lg:top-6">
                <div class="flex items-center justify-between">
                    <h2 class="font-sans text-sm font-semibold">Ketersediaan</h2>
                    @if ($mine)
                        <form method="POST" action="{{ route('favorites.toggle', $book) }}">
                            @csrf
                            <button class="btn-ghost btn-sm" aria-pressed="{{ $mine['favorite'] ? 'true' : 'false' }}">
                                <x-icon :name="$mine['favorite'] ? 'bookmark-fill' : 'bookmark'" :size="15" />
                                {{ $mine['favorite'] ? 'Tersimpan' : 'Simpan' }}
                            </button>
                        </form>
                    @endif
                </div>

                <dl class="mt-4 space-y-2.5 text-sm">
                    <div class="flex items-center justify-between">
                        <dt class="text-ink-2">Buku fisik</dt>
                        <dd>
                            @if ($book->stock > 0)
                                <span class="badge-brand">{{ $book->stock }} di rak</span>
                            @else
                                <span class="badge-neutral">Semua dipinjam</span>
                            @endif
                        </dd>
                    </div>
                    @if ($queue > 0)
                        <div class="flex items-center justify-between">
                            <dt class="text-ink-2">Antrean</dt>
                            <dd class="font-medium">{{ $queue }} orang</dd>
                        </div>
                    @endif
                    @if ($book->digital_link)
                        <div class="flex items-center justify-between">
                            <dt class="text-ink-2">E-book</dt>
                            <dd>
                                @if ($book->stock_online > 0)
                                    <span class="badge-brand">{{ $book->stock_online }} kuota baca</span>
                                @else
                                    <span class="badge-neutral">Kuota penuh</span>
                                @endif
                            </dd>
                        </div>
                    @endif
                </dl>

                <div class="mt-5 space-y-2.5 border-t border-line pt-5">
                    @guest('student')
                        <a href="{{ route('login') }}" class="btn-primary w-full">Masuk untuk meminjam</a>
                        <p class="text-center text-xs text-muted">Belum jadi anggota? <a href="{{ route('register') }}" class="font-semibold text-brand">Daftar</a></p>
                    @else
                        @if ($mine['loan']?->status === 'pending')
                            <div class="rounded-md bg-brass-tint p-3 text-sm text-brass">
                                <p class="font-semibold">Kamu punya tiket untuk buku ini.</p>
                                <p class="mt-0.5">Ambil sebelum {{ tanggal($mine['loan']->pickup_expires_at, true) }}.</p>
                            </div>
                            <a href="{{ route('borrowings.show', $mine['loan']) }}" class="btn-primary w-full"><x-icon name="qr" :size="16" /> Buka tiket</a>
                        @elseif ($mine['loan']?->status === 'active' && $mine['loan']->type === 'online')
                            <a href="{{ route('borrowings.read', $mine['loan']) }}" target="_blank" rel="noopener" class="btn-primary w-full"><x-icon name="external" :size="16" /> Lanjut baca</a>
                            <p class="text-center text-xs text-muted">Akses sampai {{ tanggal($mine['loan']->due_at, true) }}</p>
                        @elseif ($mine['loan']?->status === 'active')
                            <div class="rounded-md bg-brand-tint p-3 text-sm text-brand">
                                <p class="font-semibold">Sedang kamu pinjam.</p>
                                <p class="mt-0.5">Kembalikan sebelum {{ tanggal($mine['loan']->due_at) }}.</p>
                            </div>
                            <a href="{{ route('profile') }}" class="btn-secondary w-full">Lihat pinjamanku</a>
                        @elseif ($mine['reservation'])
                            <div class="rounded-md bg-paper-2 p-3 text-sm">
                                <p class="font-semibold">Kamu di antrean urutan ke-{{ $mine['position'] }}.</p>
                                <p class="mt-0.5 text-ink-2">Begitu eksemplar kembali dan giliranmu tiba, tiket dibuat otomatis dan kamu punya {{ config('library.reservation_pickup_hours') }} jam untuk mengambilnya.</p>
                            </div>
                            <form method="POST" action="{{ route('reservations.cancel', $mine['reservation']) }}">
                                @csrf
                                <button class="btn-secondary w-full">Keluar dari antrean</button>
                            </form>
                        @else
                            @if ($book->stock > 0)
                                <form method="POST" action="{{ route('borrow.store', $book) }}">
                                    @csrf
                                    <button class="btn-primary w-full"><x-icon name="qr" :size="16" /> Pinjam buku fisik</button>
                                </form>
                                <p class="text-xs text-muted">Kamu dapat tiket QR. Ambil di meja layanan dalam {{ config('library.pickup_hours') }} jam, lewat dari itu eksemplarnya dilepas.</p>
                            @else
                                <form method="POST" action="{{ route('reservations.store', $book) }}">
                                    @csrf
                                    <button class="btn-primary w-full"><x-icon name="queue" :size="16" /> Masuk antrean</button>
                                </form>
                                <p class="text-xs text-muted">
                                    {{ $queue > 0 ? "Kamu akan jadi urutan ke-".($queue + 1)."." : 'Kamu akan jadi yang pertama.' }}
                                    Kami kabari begitu eksemplar disisihkan untukmu.
                                </p>
                            @endif

                            @if ($book->digital_link && $book->stock_online > 0)
                                <form method="POST" action="{{ route('borrow.ebook', $book) }}">
                                    @csrf
                                    <button class="btn-secondary w-full"><x-icon name="book" :size="16" /> Pinjam e-book</button>
                                </form>
                                <p class="text-xs text-muted">Memakai 1 dari {{ $book->stock_online }} kuota baca. Akses terbuka {{ config('library.ebook_days') }} hari, lalu kuota dikembalikan otomatis.</p>
                            @endif
                        @endif
                    @endguest
                </div>
            </div>
        </aside>
    </div>

    <section class="mt-14 grid gap-10 border-t border-line pt-10 lg:grid-cols-[1fr_19rem]">
        <div>
            <h2 class="text-2xl font-semibold">Ulasan pembaca</h2>

            @if ($mine && $mine['canReview'])
                <form method="POST" action="{{ route('reviews.store', $book) }}" class="card-pad mt-5 space-y-4">
                    @csrf
                    <fieldset>
                        <legend class="label">Nilaimu</legend>
                        <div class="flex flex-row-reverse justify-end gap-1 [&>input:checked~label]:text-brass [&>label:hover]:text-brass [&>label:hover~label]:text-brass">
                            @for ($i = 5; $i >= 1; $i--)
                                <input type="radio" name="rating" value="{{ $i }}" id="rating-{{ $i }}" class="peer sr-only" required @checked(old('rating') == $i)>
                                <label for="rating-{{ $i }}" class="cursor-pointer text-line-strong" title="{{ $i }} bintang">
                                    <x-icon name="star-fill" :size="26" /><span class="sr-only">{{ $i }} bintang</span>
                                </label>
                            @endfor
                        </div>
                    </fieldset>
                    <div>
                        <label for="comment" class="label">Catatan (opsional)</label>
                        <textarea id="comment" name="comment" rows="3" maxlength="500" class="input" placeholder="Apa yang berguna dari buku ini, untuk siapa cocoknya?">{{ old('comment') }}</textarea>
                    </div>
                    <button class="btn-primary">Kirim ulasan</button>
                </form>
            @endif

            @forelse ($book->reviews as $review)
                <article class="border-b border-line py-5">
                    <div class="flex items-center gap-2">
                        <span class="flex text-brass" aria-label="{{ $review->rating }} dari 5 bintang">
                            @for ($i = 1; $i <= 5; $i++)
                                <x-icon :name="$i <= $review->rating ? 'star-fill' : 'star'" :size="14" />
                            @endfor
                        </span>
                        <span class="text-sm font-semibold">{{ $review->student->name }}</span>
                        <span class="text-xs text-muted">{{ tanggal($review->created_at) }}</span>
                    </div>
                    @if ($review->comment)
                        <p class="mt-2 text-sm leading-relaxed text-ink-2">{{ $review->comment }}</p>
                    @endif
                </article>
            @empty
                <p class="mt-4 text-sm text-muted">Belum ada ulasan. Ulasan dibuka untuk mahasiswa yang sudah meminjam dan mengembalikan buku ini.</p>
            @endforelse
        </div>

        @if ($related->isNotEmpty())
            <aside>
                <h2 class="text-lg font-semibold">Di rak yang sama</h2>
                <ul class="mt-4 space-y-4">
                    @foreach ($related as $item)
                        <li>
                            <a href="{{ route('books.show', $item) }}" class="flex gap-3 no-underline">
                                <x-book-cover :book="$item" :width="120" class="w-12 shrink-0" />
                                <span>
                                    <span class="block text-sm font-semibold text-ink hover:text-brand">{{ $item->title }}</span>
                                    <span class="block text-xs text-muted">{{ $item->author }}</span>
                                </span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </aside>
        @endif
    </section>
</x-layouts.app>
