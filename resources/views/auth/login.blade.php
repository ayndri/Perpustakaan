@extends('layout')

@section('title', 'Masuk - Perpustakaan')

@section('content')
<div class="auth-container">
    <div class="auth-header">
        <h2>Selamat Datang Kembali</h2>
        <p>Silakan masuk untuk melanjutkan</p>
    </div>

    @if($errors->any())
    <div class="alert alert-danger">
        {{ $errors->first() }}
    </div>
    @endif

    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    <form action="{{ route('login') }}" method="POST" class="auth-form">
        @csrf

        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" placeholder="Masukkan email kamu" required>

        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>

        <button type="submit" class="btn-auth">Masuk</button>
    </form>

    <div class="auth-footer">
        Belum punya akun? <a href="{{ route('register') }}">Daftar di sini</a>
    </div>
</div>
@endsection