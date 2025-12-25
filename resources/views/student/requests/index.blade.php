@extends('layout')

@section('title', 'Usulan Buku Saya')

@section('content')

<div class="container main-content mt-4">

    {{-- HEADER SECTION --}}
    <div class="d-flex justify-content-between align-items-end mb-5 fav-container">
        <div>
            <h1 style="font-weight: 800; color: #1e293b; margin: 0; font-size: 2.2rem;">
                Usulan Buku
                <span style="color: #f59e0b; font-size: 0.6em; vertical-align: super;">
                    {{ $myRequests->count() }}
                </span>
            </h1>
            <p class="text-muted mt-2 mb-0" style="font-size: 0.95rem;">Pantau status request buku Anda di sini.</p>
        </div>
        <a href="{{ route('student.requests.create') }}" class="btn btn-primary shadow-sm" style="border-radius: 50px; font-weight: 600; padding: 10px 25px; background-color: #f59e0b; border: none;">
            <i class="fas fa-plus mr-2"></i> Ajukan Baru
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success shadow-sm border-0 rounded-lg mb-4 animate-fade-in">
        <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
    </div>
    @endif

    {{-- GRID SECTION --}}
    <div class="row">
        @forelse($myRequests as $req)
        <div class="col-6 col-md-4 col-lg-3 mb-4">

            <div class="book-card-clean position-relative" style="background: transparent;">

                {{-- 1. BAGIAN COVER --}}
                <div class="book-cover mb-3 position-relative shadow-sm"
                    style="aspect-ratio: 2/3; border-radius: 15px; overflow: hidden; transition: transform 0.2s;">

                    {{-- A. STATUS BADGE (DIPINDAH KE ATAS KANAN) --}}
                    <div style="position: absolute; top: 10px; right: 10px; z-index: 20;">
                        <span class="badge badge-pill shadow-sm py-1 px-3"
                            style="font-weight: 700; font-size: 0.75rem; color: white;
                              @if($req->status == 'pending') background: #f59e0b; /* Orange */
                              @elseif($req->status == 'approved') background: #10b981; /* Hijau */
                              @elseif($req->status == 'available') background: #3b82f6; /* Biru */
                              @else background: #ef4444; /* Merah */ @endif">

                            @if($req->status == 'pending') <i style="font-size: 16px;" class="fas fa-clock mr-1"></i> Menunggu
                            @elseif($req->status == 'approved') <i style="font-size: 16px;" class="fas fa-thumbs-up mr-1"></i> Disetujui
                            @elseif($req->status == 'available') <i style="font-size: 16px;" class="fas fa-check mr-1"></i> Tersedia
                            @else <i style="font-size: 16px;" class="fas fa-times mr-1"></i> Ditolak @endif
                        </span>
                    </div>

                    {{-- B. TANGGAL (DIPINDAH KE ATAS KIRI) --}}
                    <div style="position: absolute; top: 10px; left: 10px; z-index: 20;">
                        <span class="badge badge-pill shadow-sm py-1 px-2"
                            style="background: rgba(255,255,255,0.9); backdrop-filter: blur(2px); color: #64748b; font-weight: 600; font-size: 0.7rem;">
                            {{ $req->created_at->format('d M Y') }}
                        </span>
                    </div>

                    {{-- C. TAMPILAN GAMBAR / GRADIENT --}}
                    @if($req->image)
                    {{-- Jika ada gambar --}}
                    <img src="{{ asset('storage/' . $req->image) }}" alt="{{ $req->title }}"
                        style="width: 100%; height: 100%; object-fit: cover;">
                    @else
                    {{-- Jika TIDAK ada gambar (Gradient Cover) --}}
                    <div style="width: 100%; height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 15px; text-align: center;
                            @if($req->status == 'pending') background: linear-gradient(135deg, #fcd34d 0%, #fb923c 100%);
                            @elseif($req->status == 'approved') background: linear-gradient(135deg, #6ee7b7 0%, #3b82f6 100%);
                            @elseif($req->status == 'available') background: linear-gradient(135deg, #34d399 0%, #059669 100%);
                            @else background: linear-gradient(135deg, #fca5a5 0%, #ef4444 100%); @endif">

                        {{-- Ikon Besar --}}
                        <div class="mb-2 text-white" style="font-size: 2.5rem; opacity: 0.9;">
                            <i class="fas fa-book-open"></i>
                        </div>

                        {{-- Judul di Cover (Hanya jika tidak ada gambar) --}}
                        <span class="text-white font-weight-bold px-2" style="text-shadow: 0 1px 2px rgba(0,0,0,0.1); font-size: 0.85rem; line-height: 1.4;">
                            {{ Str::limit($req->title, 40) }}
                        </span>
                    </div>
                    @endif
                </div>

                {{-- 2. BAGIAN INFO DI BAWAH --}}
                <div class="book-info pl-1">
                    {{-- Kategori --}}
                    <div class="text-uppercase font-weight-bold mb-1"
                        style="font-size: 0.7rem; color: #94a3b8; letter-spacing: 0.5px;">
                        {{ $req->category ?? 'UMUM' }}
                    </div>

                    {{-- Judul Buku --}}
                    <h5 class="font-weight-bold text-dark mb-1" style="font-size: 1.1rem; line-height: 1.3;">
                        {{ Str::limit($req->title, 30) }}
                    </h5>

                    {{-- Penulis --}}
                    <p class="text-muted mb-2" style="font-size: 0.9rem;">
                        <i class="fas fa-pen-nib mr-1" style="font-size: 0.75rem;"></i> {{ $req->author }}
                    </p>

                    {{-- Note Box (Alasan / Feedback) --}}
                    <div class="mt-2 p-2 rounded bg-light border border-light" style="font-size: 0.8rem; color: #475569;">
                        @if($req->status == 'available')
                        <span class="text-success font-weight-bold"><i class="fas fa-check-circle"></i> Sudah di rak!</span>
                        @elseif($req->status == 'rejected')
                        <span class="text-danger"><i class="fas fa-times-circle"></i> Ditolak admin.</span>
                        @else
                        <i class="fas fa-quote-left text-muted mr-1" style="font-size: 0.6rem;"></i>
                        {{ Str::limit($req->reason, 35) }}
                        @endif
                    </div>
                </div>

            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="empty-state-modern-fav text-center py-5">
                <div class="empty-icon mb-4" style="font-size: 4rem; color: #cbd5e1;">
                    <i class="fas fa-lightbulb"></i>
                </div>
                <h3 class="font-weight-bold text-dark mb-2">Belum Ada Usulan</h3>
                <p class="text-muted mb-4" style="max-width: 400px; margin: 0 auto;">
                    Butuh buku referensi yang belum tersedia? Ajukan sekarang.
                </p>
                <a href="{{ route('student.requests.create') }}" class="btn btn-primary rounded-pill px-4 py-2 font-weight-bold shadow-sm" style="background: #f59e0b; border: none;">
                    <i class="fas fa-plus mr-2"></i> Ajukan Buku Sekarang
                </a>
            </div>
        </div>
        @endforelse
    </div>
</div>
@endsection