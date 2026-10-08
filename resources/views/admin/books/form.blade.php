@php($editing = $book->exists)
<x-layouts.admin :title="$editing ? 'Ubah buku' : 'Tambah buku'">
    <div class="max-w-3xl">
        <a href="{{ route('admin.books.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-muted no-underline hover:text-ink">
            <x-icon name="arrow-left" :size="16" /> Koleksi
        </a>
        <h1 class="mt-4 text-3xl font-semibold">{{ $editing ? 'Ubah: '.$book->title : 'Tambah buku' }}</h1>

        <form method="POST" action="{{ $editing ? route('admin.books.update', $book) : route('admin.books.store') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
            @csrf
            @if ($editing) @method('PUT') @endif

            <section class="card-pad space-y-4">
                <h2 class="font-sans text-sm font-semibold">Bibliografi</h2>
                <div>
                    <label for="title" class="label">Judul</label>
                    <input id="title" name="title" value="{{ old('title', $book->title) }}" class="input" required>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="author" class="label">Penulis</label>
                        <input id="author" name="author" value="{{ old('author', $book->author) }}" class="input" required>
                    </div>
                    <div>
                        <label for="publisher" class="label">Penerbit</label>
                        <input id="publisher" name="publisher" value="{{ old('publisher', $book->publisher) }}" class="input">
                    </div>
                </div>
                <div class="grid gap-4 sm:grid-cols-3">
                    <div>
                        <label for="year" class="label">Tahun terbit</label>
                        <input id="year" name="year" type="number" value="{{ old('year', $book->year) }}" class="input" required min="1900" max="{{ now()->year + 1 }}">
                    </div>
                    <div class="sm:col-span-2">
                        <label for="isbn" class="label">ISBN</label>
                        <input id="isbn" name="isbn" value="{{ old('isbn', $book->isbn) }}" class="input font-mono" inputmode="numeric">
                    </div>
                </div>
                <div>
                    <label for="category_id" class="label">Kategori</label>
                    <select id="category_id" name="category_id" class="input" required>
                        <option value="">Pilih kategori</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected(old('category_id', $book->category_id) == $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    <p class="hint">Kategori belum ada? Tambahkan di bagian bawah halaman.</p>
                </div>
                <div>
                    <label for="description" class="label">Sinopsis</label>
                    <textarea id="description" name="description" rows="4" class="input">{{ old('description', $book->description) }}</textarea>
                </div>
            </section>

            <section class="card-pad space-y-4">
                <h2 class="font-sans text-sm font-semibold">Eksemplar dan lokasi</h2>
                <div class="grid gap-4 sm:grid-cols-3">
                    <div>
                        <label for="stock" class="label">Eksemplar di rak</label>
                        <input id="stock" name="stock" type="number" min="0" value="{{ old('stock', $book->stock ?? 0) }}" class="input" required>
                    </div>
                    <div>
                        <label for="floor" class="label">Lantai</label>
                        <input id="floor" name="floor" type="number" min="1" max="10" value="{{ old('floor', $book->floor) }}" class="input" required>
                    </div>
                    <div>
                        <label for="shelf_code" class="label">Nomor panggil</label>
                        <input id="shelf_code" name="shelf_code" value="{{ old('shelf_code', $book->shelf_code) }}" class="input font-mono" required placeholder="005.1 MAR">
                    </div>
                </div>
                @if ($editing)
                    <p class="flex items-start gap-2 rounded-md bg-brass-tint px-3 py-2.5 text-xs text-brass">
                        <x-icon name="info" :size="15" class="mt-px" />
                        Isi hanya eksemplar yang benar-benar ada di rak. Eksemplar yang sedang dipinjam atau ditahan untuk tiket sudah dihitung terpisah, jangan dijumlahkan ke sini.
                    </p>
                @endif
            </section>

            <section class="card-pad space-y-4">
                <h2 class="font-sans text-sm font-semibold">E-book <span class="font-normal text-muted">(opsional)</span></h2>
                <div class="grid gap-4 sm:grid-cols-[1fr_10rem]">
                    <div>
                        <label for="digital_link" class="label">Tautan baca</label>
                        <input id="digital_link" name="digital_link" type="url" value="{{ old('digital_link', $book->digital_link) }}" class="input" placeholder="https://">
                        <p class="hint">Hanya dibuka untuk mahasiswa yang sedang meminjam e-book.</p>
                    </div>
                    <div>
                        <label for="stock_online" class="label">Kuota baca bersamaan</label>
                        <input id="stock_online" name="stock_online" type="number" min="0" value="{{ old('stock_online', $book->stock_online ?? 0) }}" class="input" required>
                    </div>
                </div>
            </section>

            <section class="card-pad">
                <h2 class="font-sans text-sm font-semibold">Sampul</h2>
                <div class="mt-3 flex items-start gap-4">
                    @if ($editing)
                        <x-book-cover :book="$book" :width="160" class="w-20 shrink-0" />
                    @endif
                    <div class="flex-1">
                        <label for="cover" class="label">Unggah {{ $editing ? 'sampul baru' : 'sampul' }}</label>
                        <input id="cover" name="cover" type="file" accept="image/jpeg,image/png,image/webp" class="input">
                        <p class="hint">JPG, PNG, atau WebP, maksimal 2 MB.</p>
                    </div>
                </div>
            </section>

            <div class="flex items-center justify-between gap-3">
                <div class="flex gap-2">
                    <a href="{{ route('admin.books.index') }}" class="btn-secondary">Batal</a>
                    <button class="btn-primary">{{ $editing ? 'Simpan perubahan' : 'Tambah buku' }}</button>
                </div>
            </div>
        </form>

        @if ($editing)
            <form method="POST" action="{{ route('admin.books.destroy', $book) }}" class="mt-10 border-t border-line pt-6"
                  onsubmit="return confirm('Hapus {{ addslashes($book->title) }} dari koleksi?')">
                @csrf
                @method('DELETE')
                <p class="text-sm text-muted">Buku yang punya riwayat peminjaman tidak dihapus supaya laporan tetap utuh.</p>
                <button class="btn-danger btn-sm mt-2"><x-icon name="trash" :size="14" /> Hapus buku</button>
            </form>
        @endif

        <form method="POST" action="{{ route('admin.categories.store') }}" class="card-pad mt-8">
            @csrf
            <label for="category_name" class="label">Tambah kategori</label>
            <div class="flex gap-2">
                <input id="category_name" name="name" class="input" placeholder="Nama kategori baru" required>
                <button class="btn-secondary shrink-0">Tambah</button>
            </div>
        </form>
    </div>
</x-layouts.admin>
