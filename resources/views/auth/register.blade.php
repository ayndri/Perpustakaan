@extends('layout')

@section('title', 'Daftar Akun - Perpustakaan')

@section('content')
<div class="auth-container">
    <div class="auth-header">
        <h2>Buat Akun Baru</h2>
        <p>Bergabunglah untuk meminjam buku favoritmu</p>
    </div>

    <form action="{{ route('register') }}" method="POST" class="auth-form">
        @csrf

        <label class="form-label">Nama Lengkap</label>
        <input type="text" name="name" class="form-control" placeholder="Contoh: Budi Santoso" value="{{ old('name') }}" required>

        <label class="form-label">NIM</label>
        <input type="text" name="nim" class="form-control" placeholder="Nomor Induk Mahasiswa" value="{{ old('nim') }}" required>

        <label class="form-label">Jenis Kelamin</label>
        <select name="gender" class="form-control" required>
            <option value="" disabled {{ old('gender') ? '' : 'selected' }}>Pilih Jenis Kelamin</option>
            <option value="L" {{ old('gender') == 'L' ? 'selected' : '' }}>Laki-laki</option>
            <option value="P" {{ old('gender') == 'P' ? 'selected' : '' }}>Perempuan</option>
        </select>
        <label class="form-label">Jurusan</label>
        <input type="text" name="jurusan" class="form-control" placeholder="Contoh: Teknik Informatika" value="{{ old('jurusan') }}" required>

        <label class="form-label">Email Kampus</label>
        <input type="email" name="email" class="form-control" placeholder="nama@student.kampus.ac.id" value="{{ old('email') }}" required>

        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" placeholder="Minimal 6 karakter" required>

        <label class="form-label">Konfirmasi Password</label>
        <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi password" required>

        <button type="submit" class="btn-auth">Daftar Sekarang</button>
    </form>

    <div class="auth-footer">
        Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a>
    </div>
</div>
@endsection