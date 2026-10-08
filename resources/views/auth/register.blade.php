<x-layouts.auth title="Daftar anggota" class="max-w-lg">
    <div>
        <h1 class="text-3xl font-semibold">Daftar anggota</h1>
        <p class="mt-1 text-sm text-muted">Setelah mendaftar, unggah KTM untuk verifikasi. Peminjaman dibuka begitu admin menyetujui.</p>

        <form method="POST" action="{{ route('register') }}" class="card-pad mt-6 space-y-4">
            @csrf
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="nim" class="label">NIM</label>
                    <input id="nim" name="nim" value="{{ old('nim') }}" class="input font-mono" required inputmode="numeric" autocomplete="off">
                </div>
                <div>
                    <label for="jurusan" class="label">Program studi</label>
                    <input id="jurusan" name="jurusan" value="{{ old('jurusan') }}" class="input" required placeholder="Contoh: Teknik Informatika">
                </div>
            </div>
            <div>
                <label for="name" class="label">Nama lengkap</label>
                <input id="name" name="name" value="{{ old('name') }}" class="input" required autocomplete="name">
                <p class="hint">Sesuai yang tertulis di KTM.</p>
            </div>
            <div>
                <label for="email" class="label">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" class="input" required autocomplete="email">
            </div>
            <fieldset>
                <legend class="label">Jenis kelamin</legend>
                <div class="flex gap-5 text-sm">
                    <label class="flex items-center gap-2"><input type="radio" name="gender" value="P" @checked(old('gender') === 'P') class="accent-brand" required> Perempuan</label>
                    <label class="flex items-center gap-2"><input type="radio" name="gender" value="L" @checked(old('gender') === 'L') class="accent-brand"> Laki-laki</label>
                </div>
            </fieldset>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="password" class="label">Password</label>
                    <input id="password" name="password" type="password" class="input" required minlength="8" autocomplete="new-password">
                    <p class="hint">Minimal 8 karakter.</p>
                </div>
                <div>
                    <label for="password_confirmation" class="label">Ulangi password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" class="input" required autocomplete="new-password">
                </div>
            </div>
            <button class="btn-primary w-full">Daftar</button>
        </form>

        <p class="mt-4 text-center text-sm text-muted">Sudah punya akun? <a href="{{ route('login') }}" class="font-semibold text-brand">Masuk</a></p>
    </div>
</x-layouts.auth>
