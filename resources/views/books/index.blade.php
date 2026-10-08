@php
    $activeCategory = $categories->firstWhere('id', (int) request('category'));
    $filterUrl = fn ($value) => route('books.index', array_filter(['q' => $search ?: null, 'category' => request('category'), 'available' => $value]));
@endphp
<x-layouts.app :title="$activeCategory->name ?? 'Katalog'">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold sm:text-3xl">{{ $activeCategory->name ?? 'Katalog' }}</h1>
            <p class="mt-1 text-sm text-muted">
                {{ $books->total() }} {{ $search !== '' ? 'hasil untuk “'.$search.'”' : 'judul' }}
                @if ($search !== '' || $activeCategory)
                    · <a href="{{ route('books.index') }}" class="font-semibold text-brand">hapus filter</a>
                @endif
            </p>
        </div>

        {{-- Kategori dipilih dari sidebar, pencarian dari bilah atas; di sini tinggal ketersediaan. --}}
        <nav class="flex gap-1 rounded-full bg-paper-2 p-1 text-sm" aria-label="Ketersediaan">
            @foreach (['' => 'Semua', 'shelf' => 'Ada di rak', 'ebook' => 'Punya e-book'] as $value => $label)
                @php($active = (string) request('available') === (string) $value)
                <a href="{{ $filterUrl($value ?: null) }}"
                   class="rounded-full px-3.5 py-1.5 font-semibold no-underline {{ $active ? 'bg-paper text-brand shadow-sm' : 'text-ink-2 hover:text-ink' }}"
                   @if ($active) aria-current="page" @endif>{{ $label }}</a>
            @endforeach
        </nav>
    </div>

    @if ($books->isEmpty())
        <x-empty class="mt-8" title="Tidak ada buku yang cocok" icon="search">
            Coba kata kunci lain, atau usulkan judul ini supaya dibeli perpustakaan.
            <x-slot:action>
                <a href="{{ auth('student')->check() ? route('student.requests.create') : route('login') }}" class="btn-secondary">Usulkan buku</a>
            </x-slot:action>
        </x-empty>
    @else
        <div class="mt-8 grid grid-cols-2 gap-x-5 gap-y-9 sm:grid-cols-3 md:grid-cols-4 xl:grid-cols-6">
            @foreach ($books as $book)
                @include('books._card', ['book' => $book])
            @endforeach
        </div>
        <div class="mt-10">{{ $books->links() }}</div>
    @endif
</x-layouts.app>
