<x-layouts.app title="Daftar baca" wide>
    <h1 class="text-3xl font-semibold">Daftar baca</h1>
    <p class="mt-1 text-sm text-muted">Buku yang kamu simpan untuk dipinjam nanti.</p>

    @if ($favorites->isEmpty())
        <x-empty class="mt-8" title="Daftar bacamu masih kosong" icon="bookmark">
            Tekan “Simpan” di halaman buku mana pun untuk menandainya di sini.
            <x-slot:action><a href="{{ route('books.index') }}" class="btn-secondary">Buka katalog</a></x-slot:action>
        </x-empty>
    @else
        <div class="mt-8 grid grid-cols-2 gap-x-5 gap-y-9 sm:grid-cols-3 lg:grid-cols-4">
            @foreach ($favorites as $book)
                @include('books._card', ['book' => $book])
            @endforeach
        </div>
    @endif
</x-layouts.app>
