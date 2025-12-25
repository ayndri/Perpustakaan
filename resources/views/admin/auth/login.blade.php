@extends('layout')

@section('title', 'Login Administrator')

@section('content')
<div class="auth-container" style="border-top: 5px solid #2c3e50;">
    <div class="auth-header">
        <h2 style="color: #2c3e50;">Portal Admin</h2>
        <p>Silakan masuk untuk mengelola perpustakaan</p>
    </div>

    @if($errors->any())
    <div class="alert alert-danger">
        {{ $errors->first() }}
    </div>
    @endif

    <form action="{{ route('admin.login') }}" method="POST" class="auth-form">
        @csrf

        <label class="form-label">Email Administrator</label>
        <input type="email" name="email" class="form-control" placeholder="admin@perpus.com" required>

        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" placeholder="••••••••" required>

        <button type="submit" class="btn-auth" style="background-color: #2c3e50; color: white;">Masuk Dashboard</button>
    </form>

    <div class="auth-footer">
        Bukan admin? <a href="{{ route('login') }}">Login Mahasiswa di sini</a>
    </div>
</div>
@endsection