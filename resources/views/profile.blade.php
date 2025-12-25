@extends('layout')

@section('title', 'Profil Saya - PerpusKampus')

@section('content')

<div class="container main-content">

    <div class="profile-hero d-flex align-items-center flex-wrap p-4 bg-white shadow-sm rounded">

        {{-- 1. AVATAR SECTION --}}
        <div class="profile-avatar-wrapper mr-4 position-relative">
            <div class="profile-avatar rounded-circle shadow-sm" style="width: 100px; height: 100px; overflow: hidden;">
                @if($student->photo)
                <img src="{{ asset('storage/' . $student->photo) }}" style="width: 100%; height: 100%; object-fit: cover;">
                @else
                @if($student->gender == 'L')
                <img src="https://avataaars.io/?avatarStyle=Transparent&topType=ShortHairShortFlat&accessoriesType=Blank&hairColor=Black&facialHairType=Blank&clotheType=BlazerShirt&eyeType=Default&eyebrowType=Default&mouthType=Default&skinColor=Light" style="width:100%; height:100%;">
                @elseif($student->gender == 'P')
                <img src="https://avataaars.io/?avatarStyle=Transparent&topType=LongHairStraight&accessoriesType=Blank&hairColor=BrownDark&facialHairType=Blank&clotheType=BlazerShirt&eyeType=Default&eyebrowType=Default&mouthType=Default&skinColor=Light" style="width:100%; height:100%;">
                @else
                <div class="d-flex align-items-center justify-content-center h-100 bg-secondary text-white font-weight-bold" style="font-size: 2rem;">
                    {{ substr($student->name, 0, 1) }}
                </div>
                @endif
                @endif
            </div>
            {{-- Online Indicator --}}
            <div class="online-indicator position-absolute border border-white rounded-circle {{ $student->verification_status == 'verified' ? 'bg-success' : 'bg-warning' }}"
                style="width: 20px; height: 20px; bottom: 5px; right: 5px;">
            </div>
        </div>

        {{-- 2. PROFILE DETAILS --}}
        <div class="profile-details flex-grow-1">
            <h1 class="profile-name h3 font-weight-bold mb-2 text-dark">{{ $student->name }}</h1>

            <div class="profile-badges mb-3">
                {{-- Badge NIM --}}
                <span class="badge badge-pill badge-light border mr-1 px-3 py-2">
                    <i class="fas fa-id-card text-secondary mr-1"></i> {{ $student->nim }}
                </span>

                {{-- Badge Jurusan --}}
                <span class="badge badge-pill badge-primary-soft text-primary bg-light-primary mr-1 px-3 py-2" style="background: #e0f2fe; color: #0284c7;">
                    <i class="fas fa-graduation-cap mr-1"></i> {{ $student->jurusan }}
                </span>

                {{-- Badge Gender --}}
                <span class="badge badge-pill badge-light border mr-1 px-3 py-2">
                    @if($student->gender == 'L')
                    <i class="fas fa-mars text-primary"></i> Laki-laki
                    @elseif($student->gender == 'P')
                    <i class="fas fa-venus text-danger"></i> Perempuan
                    @else
                    -
                    @endif
                </span>

                {{-- Badge Status --}}
                @if($student->verification_status == 'verified')
                <span class="badge badge-pill badge-success-soft px-3 py-2" style="background:#d4edda; color:#155724; border:1px solid #c3e6cb;">
                    <i class="fas fa-check-circle mr-1"></i> Verified
                </span>
                @else
                <span class="badge badge-pill badge-warning-soft px-3 py-2" style="background:#fff3cd; color:#856404; border:1px solid #ffeeba;">
                    {{ ucfirst($student->verification_status) }}
                </span>
                @endif
            </div>
        </div>

        <div class="profile-actions d-flex align-items-center flex-wrap" style="gap: 10px;">

            <button type="button" class="btn btn-outline-primary btn-sm shadow-sm rounded-pill font-weight-bold px-3" data-toggle="modal" data-target="#editProfileModal">
                <i class="fas fa-user-edit mr-1"></i> Edit Profil
            </button>

            <a href="{{ route('favorites.index') }}" class="btn btn-light btn-sm shadow-sm rounded-pill font-weight-bold px-3 text-danger">
                <i class="fas fa-heart mr-1"></i> Favorit
            </a>

            @if($student->verification_status !== 'verified')
            <a href="{{ route('verification.index') }}" class="btn btn-primary btn-sm shadow-sm rounded-pill font-weight-bold px-3">
                <i class="fas fa-id-card-alt mr-1"></i> Verifikasi Akun
            </a>
            @endif

            <a href="{{ route('student.requests.index') }}" class="btn btn-light btn-sm shadow-sm rounded-pill font-weight-bold px-3" style="color:#f39c12;">
                <i class="fas fa-lightbulb mr-1"></i> Usulan Buku
            </a>

        </div>

    </div>

    @if(Auth::guard('student')->user()->unreadNotifications->count() > 0)
    <div class="alert alert-info shadow-sm animate-fade-in" role="alert" style="border-left: 5px solid #17a2b8;">
        <h5 class="alert-heading font-weight-bold"><i class="fas fa-bell"></i> Notifikasi Baru</h5>
        <ul class="mb-0 pl-3">
            @foreach(Auth::guard('student')->user()->unreadNotifications as $notification)
            <li style="margin-bottom: 5px;">
                {{ $notification->data['message'] }}
                <a href="{{ route('markAsRead', $notification->id) }}" class="badge badge-light ml-2">Tandai Sudah Baca</a>
            </li>
            @endforeach
        </ul>
    </div>
    @endif

    @if(session('success'))
    <div class="alert alert-success animate-fade-in">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
    @endif

    <div class="profile-grid-layout">

        <div class="left-column">

            @php
            $myReservations = $student->reservations->whereIn('status', ['pending', 'available']);
            @endphp

            @if($myReservations->count() > 0)
            <div class="status-card reservation-group" style="border: 1px solid #e0e7ff; overflow: hidden; border-radius: 15px; background: white; margin-bottom: 20px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
                <div class="card-header-status" style="background: #eef2ff; padding: 15px 20px; border-bottom: 1px solid #e0e7ff;">
                    <h3 class="status-title" style="margin: 0; font-size: 1.1rem; color: #4f46e5; font-weight: 700;">
                        <i class="fas fa-bookmark mr-2"></i> Antrian Booking Saya
                    </h3>
                </div>
                <div class="status-list" style="padding: 15px;">
                    @foreach($myReservations as $res)
                    <div class="list-item-reservation" style="display: flex; justify-content: space-between; align-items: center; padding: 15px; background: #fafafa; border-radius: 10px; border: 1px solid #f4f4f5; margin-bottom: 10px;">

                        <div class="item-info">
                            <h5 style="margin: 0; font-size: 1rem; font-weight: 700;">
                                <a href="{{ route('books.show', $res->book->id) }}" style="text-decoration: none; color: #1f2937;">
                                    {{ $res->book->title }}
                                </a>
                            </h5>

                            <div style="margin-top: 5px;">
                                @if($res->status == 'pending')
                                <span class="badge badge-warning" style="font-weight: 500; background-color: #fef3c7; color: #92400e;">
                                    <i class="fas fa-clock mr-1"></i> Menunggu Stok
                                </span>
                                @elseif($res->status == 'available')
                                <span class="badge badge-success" style="font-weight: 500; background-color: #d1fae5; color: #065f46;">
                                    <i class="fas fa-check-circle mr-1"></i> Siap Diambil
                                </span>
                                <small class="d-block text-success mt-1 font-weight-bold">Segera ke perpustakaan!</small>
                                @endif
                            </div>
                        </div>

                        <div class="item-action">
                            @if($res->status == 'pending')
                            <form action="{{ route('reservations.cancel', $res->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-danger" style="border-radius: 20px; font-size: 0.8rem;" onclick="return confirm('Batalkan booking buku ini?')">
                                    <i class="fas fa-times"></i> Batal
                                </button>
                            </form>
                            @endif
                        </div>

                    </div>
                    @endforeach
                </div>
            </div>
            @endif
            @if(isset($myRequests) && $myRequests->count() > 0)
            <div class="status-card request-group" style="border: 1px solid #fff3cd; overflow: hidden; border-radius: 15px; background: white; margin-bottom: 20px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
                <div class="card-header-status" style="background: #fffbeb; padding: 15px 20px; border-bottom: 1px solid #fff3cd; display:flex; justify-content:space-between; align-items:center;">
                    <h3 class="status-title" style="margin: 0; font-size: 1.1rem; color: #d97706; font-weight: 700;">
                        <i class="fas fa-lightbulb mr-2"></i> Usulan Saya
                    </h3>
                    <a href="{{ route('student.requests.index') }}" style="font-size: 0.8rem; text-decoration: none; color: #d97706;">Lihat Semua &rarr;</a>
                </div>
                <div class="status-list" style="padding: 15px;">
                    @foreach($myRequests as $req)
                    <div class="list-item-request" style="display: flex; justify-content: space-between; align-items: center; padding: 12px; background: #fffcf5; border-radius: 10px; border: 1px solid #fef3c7; margin-bottom: 10px;">
                        <div class="item-info">
                            <h5 style="margin: 0; font-size: 0.95rem; font-weight: 700; color: #4b5563;">
                                {{ $req->title }}
                            </h5>
                            <small class="text-muted"><i class="fas fa-pen-nib"></i> {{ $req->author }}</small>
                        </div>
                        <div class="item-status">
                            @if($req->status == 'pending')
                            <span class="badge badge-warning" style="background:#fef3c7; color:#92400e;">Review</span>
                            @elseif($req->status == 'approved')
                            <span class="badge badge-info" style="background:#e0f2fe; color:#075985;">Disetujui</span>
                            @elseif($req->status == 'available')
                            <span class="badge badge-success" style="background:#dcfce7; color:#166534;">Tersedia</span>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
            @if($pendingBorrows->count() > 0)
            <div class="status-card pending-group">
                <div class="card-header-status">
                    <h3 class="status-title warning"><i class="fas fa-hourglass-half"></i> Menunggu Persetujuan</h3>
                </div>
                <div class="status-list">
                    @foreach($pendingBorrows as $pending)
                    <div class="list-item-pending">
                        <div class="item-icon">
                            <i class="fas fa-book"></i>
                        </div>
                        <div class="item-info">
                            <h5 style="margin: 0;">
                                <a href="{{ route('books.show', $pending->book->id) }}" style="text-decoration: none; color: #334155;">
                                    {{ $pending->book->title }}
                                </a>
                            </h5>
                            <p>Diajukan: {{ \Carbon\Carbon::parse($pending->borrow_date)->format('d M Y') }}</p>

                            <div style="font-size: 0.75rem; color: #64748b; margin-top: 4px; font-family: monospace;">
                                REF: {{ $pending->ticket_number }}
                            </div>
                        </div>
                        <span class="badge-status-pill pending">Proses Admin</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <h3 class="section-heading"><i class="fas fa-book-reader"></i> Peminjaman Aktif</h3>

            @forelse($activeBorrows as $borrow)
            <div class="active-borrow-card {{ $borrow->type == 'offline' ? 'card-offline' : 'card-online' }}">

                <div class="card-top">
                    <div class="borrow-type">
                        @if($borrow->type == 'offline')
                        <i class="fas fa-box"></i> BUKU FISIK
                        @else
                        <i class="fas fa-wifi"></i> E-BOOK
                        @endif
                    </div>
                    <div class="borrow-date">
                        Mulai: {{ \Carbon\Carbon::parse($borrow->borrow_date)->format('d M Y') }}
                    </div>
                </div>

                <div class="card-content">
                    <h4 class="book-title-large">
                        <a href="{{ route('books.show', $borrow->book->id) }}" style="text-decoration: none; color: inherit; transition: color 0.2s;" onmouseover="this.style.color='#3b82f6'" onmouseout="this.style.color='inherit'">
                            {{ $borrow->book->title }}
                        </a>
                    </h4>

                    <p class="book-author-small">by {{ $borrow->book->author }}</p>

                    @if($borrow->type == 'offline')
                    @if($borrow->status == 'active')
                    <div class="ticket-container">
                        <div class="ticket-left">
                            <span class="label">NOMOR TIKET</span>
                            <span class="value" style="font-size: 1.1rem;">{{ $borrow->ticket_number }}</span>
                        </div>
                        <div class="ticket-right">
                            <span class="label">BATAS KEMBALI</span>
                            <span class="value date">{{ \Carbon\Carbon::parse($borrow->return_date)->format('d M Y') }}</span>
                        </div>
                    </div>

                    <form action="{{ route('borrow.return', $borrow->id) }}" method="POST" class="mt-3">
                        @csrf
                        <button type="submit" class="btn-action btn-return-offline" onclick="return confirm('Sudah di perpustakaan?')">
                            <i class="fas fa-undo-alt"></i> Kembalikan Buku
                        </button>
                    </form>

                    @elseif($borrow->status == 'return_pending')
                    <div class="ticket-container" style="opacity: 0.6; margin-bottom: 10px;">
                        <div class="ticket-left">
                            <span class="label">TIKET (SELESAI)</span>
                            <span class="value">{{ $borrow->ticket_number }}</span>
                        </div>
                    </div>
                    <div class="verification-box">
                        <div class="verif-icon"><i class="fas fa-user-clock"></i></div>
                        <div class="verif-text">
                            <strong>Menunggu Verifikasi Petugas</strong>
                            <p>Serahkan buku fisik ke admin.</p>
                        </div>
                    </div>
                    @endif
                    @endif
                </div>
            </div>
            @empty
            <div class="empty-state-modern">
                <img src="https://cdni.iconscout.com/illustration/premium/thumb/empty-cart-2130356-1800917.png" alt="Empty">
                <h3>Tidak ada buku dipinjam</h3>
                <p>Yuk, cari buku menarik untuk dibaca hari ini!</p>
                <a href="{{ route('books.index') }}" class="btn-explore">Jelajahi Koleksi</a>
            </div>
            @endforelse

        </div>

        <div class="right-column">
            <div class="history-panel">
                <h3 class="history-title">Riwayat Aktivitas</h3>

                <div class="history-timeline">
                    @forelse($historyBorrows as $history)
                    <div class="timeline-item">
                        <div class="timeline-marker {{ $history->status == 'rejected' ? 'rejected' : 'success' }}"></div>
                        <div class="timeline-content">
                            <h6 class="timeline-book">{{ $history->book->title }}</h6>
                            <p class="timeline-date">
                                @if($history->status == 'rejected')
                                <span class="status-rejected">Ditolak Admin</span>
                                @else
                                <span class="status-returned">Selesai: {{ \Carbon\Carbon::parse($history->updated_at)->format('d M Y') }}</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    @empty
                    <p class="text-muted text-center py-4">Belum ada riwayat.</p>
                    @endforelse
                </div>
            </div>
        </div>

    </div>
</div>

<div class="modal fade" id="editProfileModal" tabindex="-1" role="dialog"
    aria-labelledby="editProfileLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title font-weight-bold" id="editProfileLabel">Edit Profil</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    {{-- PREVIEW FOTO --}}
                    <div class="text-center mb-4">
                        <img id="photoPreview"
                            src="{{ $student->photo
                                ? asset('storage/' . $student->photo)
                                : 'https://avataaars.io/?avatarStyle=Transparent&topType=ShortHairShortFlat&clotheType=BlazerShirt&skinColor=Light' }}"
                            class="rounded-circle shadow-sm"
                            style="width:120px;height:120px;object-fit:cover;border:4px solid #e5e7eb;">
                        <p class="small text-muted mt-2">Preview Foto Profil</p>
                    </div>

                    <div class="form-group">
                        <label class="small text-muted font-weight-bold">NBI / NIM</label>
                        <input type="text" class="form-control bg-light"
                            value="{{ $student->nim }}" readonly disabled>
                    </div>

                    <div class="form-group">
                        <label class="small text-muted font-weight-bold">Email</label>
                        <input type="email" name="email"
                            class="form-control" value="{{ $student->email }}" required>
                    </div>

                    <div class="form-group">
                        <label class="small text-muted font-weight-bold">Jenis Kelamin</label>
                        <select name="gender" class="form-control" style="padding: 0px 12px;" required>
                            <option value="L" {{ $student->gender == 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ $student->gender == 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="small text-muted font-weight-bold">Nama Lengkap</label>
                        <input type="text" name="name"
                            class="form-control" value="{{ $student->name }}" required>
                    </div>

                    <div class="form-group">
                        <label class="small text-muted font-weight-bold">Ganti Foto Profil</label>
                        <div class="custom-file">
                            <input type="file"
                                class="custom-file-input"
                                id="photoUpload"
                                name="photo"
                                accept="image/*">
                            <label class="custom-file-label" for="photoUpload">Pilih foto...</label>
                        </div>
                        <small class="text-muted">Preview akan langsung muncul.</small>
                    </div>

                    <hr class="my-4">
                    <p class="font-weight-bold text-primary">
                        <i class="fas fa-lock"></i> Ganti Password
                    </p>

                    <div class="form-group">
                        <input type="password" name="password"
                            class="form-control"
                            placeholder="Password Baru (Opsional)">
                    </div>

                    <div class="form-group">
                        <input type="password" name="password_confirmation"
                            class="form-control"
                            placeholder="Ulangi Password Baru">
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button type="button" class="btn btn-light mr-2" data-dismiss="modal">
                            Batal
                        </button>
                        <button type="submit"
                            class="btn btn-primary px-4"
                            style="background:#2c3e50;border:none;">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('photoUpload').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;

        e.target.nextElementSibling.innerText = file.name;

        const reader = new FileReader();
        reader.onload = function(event) {
            document.getElementById('photoPreview').src = event.target.result;
        };
        reader.readAsDataURL(file);
    });
</script>

@endsection