@extends('admin.layout')

@section('content')

<h1 class="h3 mb-4 text-gray-800 font-weight-bold">Input Mahasiswa Offline</h1>

@if(session('success'))
<div class="alert alert-success border-left-success shadow-sm">
    <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
</div>
@endif

<div class="row">
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Form Pendaftaran Mahasiswa</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.students.store') }}" method="POST">
                    @csrf

                    <div class="form-group">
                        <label class="font-weight-bold">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Alamat Email</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="contoh@mahasiswa.univ.ac.id" required>
                        @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">NIM / NBI</label>
                        <input type="text" name="nim" class="form-control @error('nim') is-invalid @enderror" value="{{ old('nim') }}" required>
                        @error('nim')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Jenis Kelamin</label>
                        <select name="gender" class="form-control @error('gender') is-invalid @enderror" required>
                            <option value="">-- Pilih Jenis Kelamin --</option>
                            <option value="L" {{ old('gender') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ old('gender') == 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                        @error('gender')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Jurusan</label>
                        <select name="jurusan" class="form-control @error('jurusan') is-invalid @enderror">
                            <option value="">-- Pilih Jurusan --</option>
                            <option value="Teknik Informatika">Teknik Informatika</option>
                            <option value="Sistem Informasi">Sistem Informasi</option>
                            <option value="Teknik Sipil">Teknik Sipil</option>
                            <option value="Manajemen">Manajemen</option>
                            <option value="Akuntansi">Akuntansi</option>
                        </select>
                        @error('jurusan')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="alert alert-warning">
                        <i class="fas fa-key mr-1"></i> Password akun akan diset otomatis menjadi: <strong>password123</strong>
                    </div>

                    <hr>

                    <button type="submit" class="btn btn-primary btn-block font-weight-bold">
                        <i class="fas fa-save mr-2"></i> Daftarkan Mahasiswa
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card shadow mb-4 border-left-info">
            <div class="card-body">
                <h5 class="font-weight-bold text-info"><i class="fas fa-info-circle"></i> Catatan Admin</h5>
                <p>Fitur ini digunakan untuk mendaftarkan mahasiswa yang datang langsung ke perpustakaan.</p>
                <ul class="pl-3">
                    <li>Akun yang dibuat di sini akan otomatis berstatus <span class="badge badge-success">Verified</span>.</li>
                    <li>Mahasiswa tidak perlu mengunggah KTM lagi.</li>
                    <li>Berikan email/NIM dan password kepada mahasiswa agar mereka bisa login.</li>
                </ul>
            </div>
        </div>
    </div>
</div>

@endsection