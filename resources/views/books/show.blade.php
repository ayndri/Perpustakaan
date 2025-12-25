@extends('layout')

@section('title', 'Detail Buku - ' . $book->title)

@section('content')

<div class="container main-content">

    @if(session('success'))
    <div class="alert alert-success" style="margin-bottom: 20px;">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger" style="margin-bottom: 20px;">
        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
    </div>
    @endif

    <div class="breadcrumb">
        <a href="{{ route('home') }}">Home</a>
        <span>/</span>
        <a href="{{ route('books.index') }}">Koleksi</a>
        <span>/</span>
        <span class="current">{{ Str::limit($book->title, 20) }}</span>
    </div>

    <div class="book-detail-card">
        <div class="detail-grid">

            <div class="detail-left">
                <div class="book-cover-large" style="padding: 0; overflow: hidden; position: relative; background-color: #f0f0f0;">

                    @if(Auth::guard('student')->check())
                    @php
                    $isFav = Auth::guard('student')->user()->favorites->contains($book->id);
                    @endphp
                    <form action="{{ route('favorites.toggle', $book->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn-wishlist-detail" title="{{ $isFav ? 'Hapus dari Favorit' : 'Tambah ke Favorit' }}"
                            style="position: absolute; top: 15px; right: 15px; z-index: 20; background: rgba(255,255,255,0.9); border-radius: 50%; width: 45px; height: 45px; display: flex; align-items: center; justify-content: center; border: none; box-shadow: 0 4px 10px rgba(0,0,0,0.15); cursor: pointer;">
                            <i class="fas fa-heart {{ $isFav ? 'active' : 'inactive' }}" style="font-size: 1.2rem; color: #e74c3c;"></i>
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
                        <i class="fas fa-laptop-code text-white" style="font-size: 5rem; opacity: 0.8;"></i>
                        @elseif($book->category->name == 'Fiksi')
                        <i class="fas fa-dragon text-white" style="font-size: 5rem; opacity: 0.8;"></i>
                        @elseif($book->category->name == 'Bisnis')
                        <i class="fas fa-chart-line text-white" style="font-size: 5rem; opacity: 0.8;"></i>
                        @else
                        <i class="fas fa-book text-white" style="font-size: 5rem; opacity: 0.8;"></i>
                        @endif
                    </div>
                    @endif
                </div>
            </div>

            <div class="detail-right">
                <div class="book-meta">
                    <span class="badge-category">{{ $book->category->name }}</span>
                    <span class="text-year">{{ $book->year }}</span>
                </div>

                <h1 class="detail-title">{{ $book->title }}</h1>
                <p class="detail-author">Ditulis oleh <span style="color: #2c3e50; font-weight: 600;">{{ $book->author }}</span></p>

                <hr class="divider">

                @if($book->stock > 0 || $book->stock == 0)
                <div class="location-box mb-4 p-3" style="background: #f8f9fc; border-radius: 10px; border-left: 4px solid #4e73df;">
                    <div class="d-flex align-items-center">
                        <div class="icon-loc mr-3 text-primary" style="font-size: 2rem;">
                            <i class="fas fa-map-marked-alt"></i>
                        </div>
                        <div>
                            <h6 class="font-weight-bold text-gray-800 m-0">Lokasi Penyimpanan</h6>
                            <div style="font-size: 0.95rem; color: #555;">
                                Lantai <span class="font-weight-bold text-dark">{{ $book->floor }}</span> &bull;
                                Rak <span class="font-weight-bold text-dark">{{ $book->shelf_code }}</span>
                            </div>
                            <small class="text-muted"><i class="fas fa-info-circle"></i> Gunakan kode rak ini untuk menemukan buku.</small>
                        </div>
                    </div>
                </div>
                @endif

                <div class="description-box">
                    <div style="margin-bottom: 15px; display: flex; flex-wrap: wrap; gap: 10px;">
                        @if($book->stock > 0)
                        <span class="badge-stock available"><i class="fas fa-box"></i> Fisik: {{ $book->stock }}</span>
                        @else
                        <span class="badge-stock out-of-stock"><i class="fas fa-times"></i> Fisik Habis</span>
                        @endif

                        @if($book->digital_link)
                        @if($book->stock_online > 0)
                        <span class="badge-stock online-ready"><i class="fas fa-wifi"></i> Online: {{ $book->stock_online }} Slot</span>
                        @else
                        <span class="badge-stock out-of-stock"><i class="fas fa-ban"></i> Kuota Online Penuh</span>
                        @endif
                        @else
                        <span class="badge-stock offline-only"><i class="fas fa-unlink"></i> Tidak Ada Versi E-Book</span>
                        @endif
                    </div>

                    <h3>Sinopsis</h3>
                    <p>{{ $book->description ?? 'Tidak ada deskripsi untuk buku ini.' }}</p>
                </div>

                <div class="action-area" style="flex-wrap: wrap;">
                    @if(Auth::guard('student')->check())
                    @php
                    $studentId = Auth::guard('student')->id();
                    $ongoingBorrow = \App\Models\Borrowing::where('student_id', $studentId)
                    ->where('book_id', $book->id)
                    ->whereIn('status', ['active', 'pending', 'return_pending'])
                    ->first();
                    @endphp

                    @if($ongoingBorrow)
                    {{-- LOGIKA JIKA SEDANG MEMINJAM --}}
                    @if($ongoingBorrow->status == 'pending')
                    <button class="btn-borrow-locked" style="flex: 1; cursor: default; background: #f39c12; color: white; border:none;">
                        <i class="fas fa-clock"></i> Menunggu Persetujuan
                    </button>
                    @elseif($ongoingBorrow->status == 'return_pending')
                    <button class="btn-borrow-locked" style="flex: 1; cursor: default; background: #f39c12; color: white; border:none;">
                        <i class="fas fa-hourglass-half"></i> Menunggu Verifikasi Admin
                    </button>
                    @elseif($ongoingBorrow->status == 'active')
                    <form action="{{ route('borrow.return', $ongoingBorrow->id) }}" method="POST" style="flex: 1;">
                        @csrf
                        @if($ongoingBorrow->type == 'offline')
                        <button type="submit" class="btn-borrow" style="background-color: #e74c3c; border-color: #e74c3c;" onclick="return confirm('Apakah Anda sudah di perpustakaan dan ingin mengembalikan buku ini?')">
                            <i class="fas fa-undo"></i> Kembalikan Buku Fisik
                        </button>
                        @else
                        <button type="submit" class="btn-borrow" style="background-color: #2ecc71; border-color: #2ecc71;" onclick="return confirm('Selesai membaca e-book ini?')">
                            <i class="fas fa-check"></i> Selesai Membaca
                        </button>
                        @endif
                    </form>
                    @endif

                    @else
                    {{-- JIKA TIDAK SEDANG MEMINJAM --}}

                    {{-- 1. CEK STOK FISIK --}}
                    @if($book->stock > 0)
                    <form action="{{ route('borrow.store', $book->id) }}" method="POST" style="flex: 1;">
                        @csrf
                        <input type="hidden" name="type" value="offline">
                        <button type="submit" class="btn-borrow" onclick="return confirm('Pinjam buku fisik ini?')">
                            <i class="fas fa-shopping-bag"></i> Pinjam Fisik
                        </button>
                    </form>
                    @else
                    {{-- STOK HABIS: CEK RESERVASI --}}
                    @php
                    $isReserved = \App\Models\Reservation::where('student_id', Auth::guard('student')->id())
                    ->where('book_id', $book->id)
                    ->whereIn('status', ['pending', 'available'])
                    ->exists();
                    @endphp

                    @if($isReserved)
                    <button class="btn-borrow-locked" style="flex: 1; cursor: default; background: #3498db; color: white; border:none;">
                        <i class="fas fa-clock"></i> Sudah Masuk Antrian
                    </button>
                    @else
                    <form action="{{ route('reservations.store', $book->id) }}" method="POST" style="flex: 1;">
                        @csrf
                        <button type="submit" class="btn-borrow" style="background-color: #6f42c1; border-color: #6f42c1;" onclick="return confirm('Stok habis. Ingin masuk daftar antrian (booking)?')">
                            <i class="fas fa-bookmark"></i> Booking Buku
                        </button>
                    </form>
                    @endif
                    @endif

                    {{-- 2. CEK DIGITAL --}}
                    @if($book->digital_link)
                    @if($book->stock_online > 0)
                    <form action="{{ route('borrow.store', $book->id) }}" method="POST" style="flex: 1;">
                        @csrf
                        <input type="hidden" name="type" value="online">
                        <button type="submit" class="btn-borrow online-btn">
                            <i class="fas fa-cloud-download-alt"></i> Pinjam Online
                        </button>
                    </form>
                    @else
                    <button class="btn-borrow-locked" style="flex: 1; cursor: not-allowed; opacity: 0.6;">
                        <i class="fas fa-ban"></i> Kuota Online Penuh
                    </button>
                    @endif
                    @endif

                    @endif

                    @else
                    {{-- BELUM LOGIN --}}
                    <a href="{{ route('login') }}" class="btn-borrow-locked" style="width: 100%;">
                        <i class="fas fa-lock"></i> Login untuk Akses Buku
                    </a>
                    @endif
                </div>

            </div>
        </div>
    </div>

    <div class="review-section-card" style="background: white; border-radius: 15px; padding: 30px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-top: 40px; border: 1px solid #f0f0f0;">

        <div class="d-flex align-items-center justify-content-between mb-4 border-bottom pb-3">
            <div>
                <h3 class="m-0" style="font-weight: 700; color: #2c3e50;">Ulasan Pembaca</h3>
                <p class="text-muted m-0">Apa kata mereka tentang buku ini?</p>
            </div>
            <div class="avg-rating text-right">
                <span class="text-warning display-4 font-weight-bold">
                    {{ $book->average_rating ?: '0.0' }}
                </span>
                <div class="small text-muted">/ 5.0 dari {{ $book->reviews->count() }} ulasan</div>
            </div>
        </div>

        @if(Auth::guard('student')->check())
        @php
        $studentId = Auth::guard('student')->id();
        $hasReturned = \App\Models\Borrowing::where('student_id', $studentId)
        ->where('book_id', $book->id)
        ->where('status', 'returned')
        ->exists();
        $hasReviewed = \App\Models\Review::where('student_id', $studentId)
        ->where('book_id', $book->id)
        ->exists();
        @endphp

        @if($hasReturned && !$hasReviewed)
        <div class="card p-4 mb-5 bg-light border-0" style="border-radius: 12px;">
            <h5 class="font-weight-bold mb-3" style="color: #2c3e50;">Bagikan pengalamanmu</h5>
            <form action="{{ route('reviews.store', $book->id) }}" method="POST">
                @csrf
                <div class="star-rating-input mb-2">
                    <input type="radio" id="star5" name="rating" value="5" /><label for="star5" title="Sempurna"><i class="fas fa-star"></i></label>
                    <input type="radio" id="star4" name="rating" value="4" /><label for="star4" title="Bagus"><i class="fas fa-star"></i></label>
                    <input type="radio" id="star3" name="rating" value="3" /><label for="star3" title="Oke"><i class="fas fa-star"></i></label>
                    <input type="radio" id="star2" name="rating" value="2" /><label for="star2" title="Buruk"><i class="fas fa-star"></i></label>
                    <input type="radio" id="star1" name="rating" value="1" /><label for="star1" title="Sangat Buruk"><i class="fas fa-star"></i></label>
                </div>
                <textarea name="comment" class="form-control mb-3" rows="3" placeholder="Tulis pendapatmu di sini..." required style="border-radius: 10px; border: 1px solid #ddd;"></textarea>
                <button type="submit" class="btn btn-primary px-4 py-2" style="background: #2c3e50; border:none; border-radius: 50px; font-weight: 600;">
                    <i class="fas fa-paper-plane mr-2"></i> Kirim Ulasan
                </button>
            </form>
        </div>
        @endif
        @endif

        <div class="review-list">
            @forelse($book->reviews as $review)
            <div class="review-item mb-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="d-flex align-items-center">
                        <div class="avatar-wrapper-modern">
                            {{ substr($review->student->name, 0, 1) }}
                        </div>
                        <div>
                            <h6 class="font-weight-bold mb-0">{{ $review->student->name }}</h6>
                            <div class="text-warning small">
                                @for($i=1; $i<=5; $i++)
                                    <i class="fas fa-star {{ $i <= $review->rating ? '' : 'text-muted' }}"></i>
                                    @endfor
                            </div>
                        </div>
                    </div>
                    <small class="text-muted font-weight-medium">{{ $review->created_at->diffForHumans() }}</small>
                </div>

                <div class="review-content-box mt-3">
                    <p class="mb-0">{{ $review->comment }}</p>
                </div>
            </div>
            @empty
            <div class="text-center py-5 animate-fade-in">
                <div style="font-size: 3rem; color: #e2e8f0; margin-bottom: 15px;">
                    <i class="far fa-comments"></i>
                </div>
                <h5 style="color: #64748b; font-weight: 600;">Belum ada ulasan</h5>
                <p class="text-muted small">Jadilah yang pertama memberikan ulasan untuk buku ini!</p>
            </div>
            @endforelse
        </div>
    </div>

</div>
@endsection