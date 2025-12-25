@extends('layout')

@section('title', 'Koleksi Favorit Saya')

@section('content')

<div class="container main-content mt-4">

    <div class="d-flex justify-content-between align-items-end mb-5 fav-container">
        <div>
            <h1 style="font-weight: 800; color: #1e293b; margin: 0; font-size: 2.2rem;">
                Koleksi Favorit
                <span style="color: #e74c3c; font-size: 0.6em; vertical-align: super;">
                    {{ $favorites->count() }}
                </span>
            </h1>
        </div>
        <a href="{{ route('profile') }}" class="btn btn-light shadow-sm" style="border-radius: 50px; font-weight: 600; padding: 10px 25px; color: #475569;">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success shadow-sm border-0 rounded-lg mb-4 animate-fade-in">
        <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
    </div>
    @endif

    <div class="book-grid">
        @forelse($favorites as $book)

        <div class="book-card-clean">

            <div class="book-cover-clean" style="padding: 0; overflow: hidden; position: relative; background-color: #f0f0f0;">

                @if(Auth::guard('student')->check())
                @php
                $isFav = Auth::guard('student')->user()->favorites->contains($book->id);
                @endphp

                <form action="{{ route('favorites.toggle', $book->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-wishlist-circle" title="Hapus Favorit" style="position: absolute; top: 10px; right: 10px; z-index: 20; background: rgba(255,255,255,0.9); border-radius: 50%; width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; border: none; box-shadow: 0 2px 5px rgba(0,0,0,0.1); cursor: pointer;">
                        <i class="fas fa-heart {{ $isFav ? 'active' : 'inactive' }}" style="color: #e74c3c;"></i>
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

                    <div class="center-icon">
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
                </div>
                @endif
            </div>

            <div class="book-info-clean">
                <div class="cat-text">{{ $book->category->name }}</div>

                <h4 class="title-text">
                    <a href="{{ route('books.show', $book->id) }}">{{ Str::limit($book->title, 35) }}</a>
                </h4>

                <p class="author-text">{{ $book->author }}</p>

                <a href="{{ route('books.show', $book->id) }}" class="link-detail">
                    Lihat Detail &rarr;
                </a>
            </div>
        </div>

        @empty
        <div class="empty-state-modern-fav" style="grid-column: 1/-1;">
            <div class="empty-content text-center py-5">
                <div class="empty-icon mb-4" style="font-size: 4rem; color: #cbd5e1;">
                    <i class="fas fa-heart-broken"></i>
                </div>

                <h3 class="font-weight-bold text-dark mb-2">Belum Ada Koleksi Favorit</h3>
                <p class="text-muted mb-4" style="max-width: 400px; margin: 0 auto;">
                    Sepertinya Anda belum menandai buku apapun. Yuk, jelajahi koleksi kami dan simpan buku yang Anda suka di sini!
                </p>

                <a href="{{ route('books.index') }}" class="btn btn-primary rounded-pill px-4 py-2 font-weight-bold shadow-sm" style="background: #3b82f6; border: none;">
                    <i class="fas fa-search mr-2"></i> Jelajahi Buku
                </a>
            </div>
        </div>
        @endforelse
    </div>
</div>
@endsection