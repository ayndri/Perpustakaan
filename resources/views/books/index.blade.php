@extends('layout')

@section('title', 'Koleksi Buku - Perpustakaan')

@section('content')

<div class="page-header">
    <div class="container">
        <h1>Jelajahi Koleksi</h1>
        <p>Temukan buku favoritmu dari ribuan arsip perpustakaan kami.</p>

        <form action="{{ route('books.index') }}" method="GET" class="search-bar-container">

            <div class="search-select-wrapper">
                <i class="fas fa-filter icon"></i>
                <select name="category" class="search-select">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="search-input-wrapper">
                <i class="fas fa-search icon"></i>
                <input type="text" name="search" class="search-input" placeholder="Cari judul buku..." value="{{ request('search') }}">
            </div>

            <button type="submit" class="btn-search">Cari</button>
        </form>
    </div>
</div>

<div class="container main-content">

    @if(request('search') || request('category'))
    <div class="search-result-info">
        Menampilkan hasil untuk:
        <strong>{{ request('search') ?: 'Semua Judul' }}</strong>
        @if(request('category'))
        di kategori <strong>{{ $categories->find(request('category'))->name }}</strong>
        @endif
    </div>
    @endif

    <div class="book-grid">
        @forelse($books as $book)
        <div class="book-card">

            <div class="book-cover" style="padding: 0; overflow: hidden; position: relative; background-color: #f0f0f0;">

                @if(Auth::guard('student')->check())
                @php
                $isFav = Auth::guard('student')->user()->favorites->contains($book->id);
                @endphp

                <form action="{{ route('favorites.toggle', $book->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-wishlist" style="position: absolute; top: 10px; right: 10px; z-index: 20; background: rgba(255,255,255,0.8); border-radius: 50%; padding: 5px 8px;">
                        <i class="fas fa-heart {{ $isFav ? 'active' : 'inactive' }}"></i>
                    </button>
                </form>
                @endif

                @if($book->cover)
                <img src="{{ asset('storage/' . $book->cover) }}" alt="{{ $book->title }}" style="width: 100%; height: 100%; object-fit: cover;">
                @else
                <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center;
                        @if($book->category->name == 'Pemrograman') background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
                        @elseif($book->category->name == 'Fiksi') background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 99%, #fecfef 100%);
                        @elseif($book->category->name == 'Bisnis') background: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%);
                        @else background: linear-gradient(135deg, #a18cd1 0%, #fbc2eb 100%); @endif">

                    @if($book->category->name == 'Pemrograman')
                    <i class="fas fa-laptop-code text-white" style="font-size: 3rem; opacity: 0.8;"></i>
                    @elseif($book->category->name == 'Fiksi')
                    <i class="fas fa-dragon text-white" style="font-size: 3rem; opacity: 0.8;"></i>
                    @elseif($book->category->name == 'Bisnis')
                    <i class="fas fa-chart-line text-white" style="font-size: 3rem; opacity: 0.8;"></i>
                    @else
                    <i class="fas fa-book text-white" style="font-size: 3rem; opacity: 0.8;"></i>
                    @endif
                </div>
                @endif
            </div>

            <div class="book-info">
                <span class="book-category">{{ $book->category->name }}</span>

                <h3 class="book-title">
                    <a href="{{ route('books.show', $book->id) }}" style="text-decoration: none; color: inherit;">
                        {{ Str::limit($book->title, 40) }}
                    </a>
                </h3>

                <p class="book-author">{{ $book->author }}</p>

                <a href="{{ route('books.show', $book->id) }}" style="font-size: 0.8rem; color: #5dade2; font-weight: 600;">
                    Lihat Detail &rarr;
                </a>
            </div>
        </div>
        @empty

        <div class="empty-state">
            <img src="https://cdni.iconscout.com/illustration/premium/thumb/search-result-not-found-2130361-1800925.png" alt="Not Found" style="width: 200px; opacity: 0.8;">
            <h3>Buku tidak ditemukan</h3>
            <p>Coba gunakan kata kunci lain atau reset filter kategori.</p>
            <a href="{{ route('books.index') }}" class="btn-reset">Reset Pencarian</a>
        </div>
        @endforelse
    </div>

    <div class="pagination-wrapper">
        {{ $books->withQueryString()->links() }}
    </div>

</div>

@endsection