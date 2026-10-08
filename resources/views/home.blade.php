<x-layouts.app>
    <section class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold sm:text-3xl">
                @auth('student')
                    Halo, {{ \Illuminate\Support\Str::of(auth('student')->user()->name)->explode(' ')->first() }}. Mau baca apa hari ini?
                @else
                    Mau baca apa hari ini?
                @endauth
            </h1>
            <p class="mt-1 text-sm text-muted">{{ $totalBooks }} judul di rak · {{ $ebookCount }} bisa dibaca daring · pesan dari sini, ambil di meja dengan QR</p>
        </div>
        <a href="{{ route('books.index') }}" class="btn-secondary">Buka katalog lengkap</a>
    </section>

    @foreach ($shelves as $shelf)
        <section class="mt-10" aria-labelledby="rak-{{ $loop->index }}">
            <div class="flex items-baseline justify-between gap-4">
                <div class="flex flex-wrap items-baseline gap-x-3">
                    <h2 id="rak-{{ $loop->index }}" class="text-base font-extrabold uppercase tracking-wide">{{ $shelf['title'] }}</h2>
                    @if ($shelf['caption'])
                        <span class="text-xs text-muted">{{ $shelf['caption'] }}</span>
                    @endif
                </div>
                @isset($shelf['link'])
                    <a href="{{ $shelf['link'] }}" class="shrink-0 text-sm font-semibold text-brand">Lihat semua</a>
                @endisset
            </div>

            {{-- Rak horizontal tanpa scrollbar: digeser dengan sentuhan/trackpad, atau tombol panah di desktop. --}}
            <div class="group/shelf relative mt-4" data-shelf>
                <div class="no-scrollbar flex snap-x snap-mandatory gap-5 overflow-x-auto scroll-smooth pb-2" data-track>
                    @foreach ($shelf['books'] as $book)
                        <div class="w-32 shrink-0 snap-start sm:w-36">
                            @include('books._card', ['book' => $book])
                        </div>
                    @endforeach
                </div>

                @foreach (['prev' => ['left-0 -translate-x-1/2', 'arrow-left', 'Geser ke kiri'], 'next' => ['right-0 translate-x-1/2', 'arrow-right', 'Geser ke kanan']] as $dir => [$pos, $icon, $label])
                    <button type="button" data-{{ $dir }}
                            class="absolute top-[5.5rem] z-10 hidden h-10 w-10 place-items-center rounded-full border border-line bg-paper text-ink shadow-md shadow-ink/15 opacity-0 transition-opacity duration-150 hover:text-brand focus-visible:opacity-100 group-hover/shelf:opacity-100 disabled:!opacity-0 sm:top-24 md:grid {{ $pos }}">
                        <x-icon :name="$icon" :size="18" />
                        <span class="sr-only">{{ $label }}</span>
                    </button>
                @endforeach
            </div>
        </section>
    @endforeach

    <script>
        // Tombol panah menggeser rak sejauh lebar yang terlihat dan mati sendiri di ujung rak.
        document.querySelectorAll('[data-shelf]').forEach((shelf) => {
            const track = shelf.querySelector('[data-track]');
            const prev = shelf.querySelector('[data-prev]');
            const next = shelf.querySelector('[data-next]');
            const update = () => {
                prev.disabled = track.scrollLeft <= 4;
                next.disabled = track.scrollLeft + track.clientWidth >= track.scrollWidth - 4;
            };
            prev.addEventListener('click', () => track.scrollBy({ left: -track.clientWidth * 0.85 }));
            next.addEventListener('click', () => track.scrollBy({ left: track.clientWidth * 0.85 }));
            track.addEventListener('scroll', update, { passive: true });
            window.addEventListener('resize', update);
            update();
        });
    </script>
</x-layouts.app>
