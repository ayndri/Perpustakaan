<x-layouts.admin title="Koleksi">
    <div class="max-w-6xl">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-3xl font-semibold">Koleksi</h1>
                <p class="mt-1 text-sm text-muted">{{ $books->total() }} judul</p>
            </div>
            <a href="{{ route('admin.books.create') }}" class="btn-primary"><x-icon name="plus" :size="16" /> Tambah buku</a>
        </div>

        <form method="GET" class="mt-5 flex flex-wrap gap-2" role="search">
            <label for="q" class="sr-only">Cari</label>
            <input id="q" name="q" value="{{ $search }}" class="input w-72" placeholder="Judul, penulis, atau ISBN">
            <label for="category" class="sr-only">Kategori</label>
            <select id="category" name="category" class="input w-56">
                <option value="">Semua kategori</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected(request('category') == $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>
            <button class="btn-secondary">Terapkan</button>
        </form>

        @if ($books->isEmpty())
            <x-empty class="mt-6" title="Belum ada buku yang cocok" icon="book">
                <x-slot:action><a href="{{ route('admin.books.create') }}" class="btn-secondary">Tambah buku</a></x-slot:action>
            </x-empty>
        @else
            <div class="table-wrap mt-5">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Buku</th>
                            <th>Rak</th>
                            <th class="text-right" title="Eksemplar bebas di rak">Di rak</th>
                            <th class="text-right" title="Tiket menunggu + sedang dipinjam">Keluar</th>
                            <th class="text-right">Antre</th>
                            <th class="text-right">E-book</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($books as $book)
                            <tr>
                                <td>
                                    <div class="flex items-center gap-3">
                                        <x-book-cover :book="$book" :width="80" class="w-9 shrink-0" />
                                        <div class="min-w-0">
                                            <a href="{{ route('books.show', $book) }}" class="font-medium text-ink">{{ $book->title }}</a>
                                            <span class="block text-xs text-muted">{{ $book->author }} · {{ $book->category->name }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap"><span class="spine">{{ $book->shelfLabel() ?? '–' }}</span></td>
                                <td class="text-right font-semibold {{ $book->stock === 0 ? 'text-danger' : '' }}">{{ $book->stock }}</td>
                                <td class="text-right text-ink-2">{{ $book->on_loan }}</td>
                                <td class="text-right {{ $book->waiting ? 'font-semibold text-brass' : 'text-ink-2' }}">{{ $book->waiting ?: '–' }}</td>
                                <td class="text-right text-ink-2">{{ $book->digital_link ? $book->stock_online : '–' }}</td>
                                <td class="whitespace-nowrap text-right">
                                    <a href="{{ route('admin.books.edit', $book) }}" class="btn-ghost btn-sm"><x-icon name="edit" :size="14" /> Ubah</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-5">{{ $books->links() }}</div>
        @endif
    </div>
</x-layouts.admin>
