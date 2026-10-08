<x-layouts.auth title="Masuk">
    <div>
        <h1 class="text-3xl font-semibold">Masuk</h1>
        <p class="mt-1 text-sm text-muted">Pakai email yang kamu daftarkan sebagai anggota.</p>

        <form method="POST" action="{{ route('login') }}" class="card-pad mt-6 space-y-4">
            @csrf
            <div>
                <label for="email" class="label">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" class="input" required autofocus autocomplete="email">
            </div>
            <div>
                <label for="password" class="label">Password</label>
                <input id="password" name="password" type="password" class="input" required autocomplete="current-password">
            </div>
            <label class="flex items-center gap-2 text-sm text-ink-2">
                <input type="checkbox" name="remember" value="1" class="accent-brand"> Ingat saya di perangkat ini
            </label>
            <button class="btn-primary w-full">Masuk</button>
        </form>

        <p class="mt-4 text-center text-sm text-muted">Belum jadi anggota? <a href="{{ route('register') }}" class="font-semibold text-brand">Daftar</a></p>

        <div class="mt-8 rounded-md border border-dashed border-line-strong px-4 py-3 text-xs text-ink-2">
            <p class="font-semibold text-ink">Akun demo</p>
            <p class="mt-1">Mahasiswa: <span class="font-mono">dewi@mhs.test</span> · <span class="font-mono">password123</span></p>
            <p>Admin: <a href="{{ route('admin.login') }}" class="text-brand">/admin</a> dengan <span class="font-mono">admin@perpus.test</span></p>
        </div>
    </div>
</x-layouts.auth>
