@extends('layout')

@section('title', 'Home - Perpustakaan Kampus')

@section('content')

<section class="hero-modern">
    <div class="hero-content">
        <h1>Jelajahi Ribuan Buku <br>untuk <span class="text-highlight">Masa Depanmu</span></h1>
        <p>Akses koleksi buku terlengkap dari berbagai kategori. Mulai dari Teknologi, Bisnis, hingga Novel Fiksi terbaik.</p>
        <a href="{{ route('books.index') }}" class="btn-hero">Cari Buku Sekarang</a>
    </div>
</section>

<div class="container main-content">

    <div class="section-header">
        <h2>Buku Terpopuler 🔥</h2>
        <p>Paling banyak dipinjam minggu ini</p>
    </div>

    <div class="book-grid">
        @forelse($topBooks as $book)
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
                <div style="width: 100%; height: 100%; background: linear-gradient(135deg, #a18cd1 0%, #fbc2eb 100%); display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-book-open text-white" style="font-size: 3rem; opacity: 0.7;"></i>
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

                <a href="{{ route('books.show', $book->id) }}" style="font-size: 0.8rem; color: #5dade2; font-weight: 600;">Lihat Detail &rarr;</a>
            </div>
        </div>
        @empty
        <div class="empty-state">
            <p>Belum ada data buku.</p>
        </div>
        @endforelse
    </div>


    <div class="section-header mt-large">
        <h2>Rekomendasi Untukmu 📚</h2>
        <p>Pilihan kurator perpustakaan spesial untuk kamu</p>
    </div>

    <div class="book-grid">
        @forelse($featuredBooks as $book)
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
                <div style="width: 100%; height: 100%; background: linear-gradient(120deg, #f6d365 0%, #fda085 100%); display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-star text-white" style="font-size: 3rem; opacity: 0.7;"></i>
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

                <a href="{{ route('books.show', $book->id) }}" style="font-size: 0.8rem; color: #5dade2; font-weight: 600;">Lihat Detail &rarr;</a>
            </div>
        </div>
        @empty
        <div class="empty-state">
            <p>Belum ada rekomendasi.</p>
        </div>
        @endforelse
    </div>

</div>

@endsection