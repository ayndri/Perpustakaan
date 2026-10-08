<x-layouts.app title="Pengaturan akun">
    <div class="mx-auto max-w-xl">
        <h1 class="text-3xl font-semibold">Pengaturan akun</h1>
        <p class="mt-1 text-sm text-muted">NIM dan jurusan hanya bisa diubah admin, karena dipakai untuk verifikasi.</p>

        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="card-pad mt-6 space-y-5">
            @csrf
            @method('PUT')

            <div class="flex items-center gap-4">
                @if ($student->photoUrl())
                    <img src="{{ $student->photoUrl() }}" alt="Foto profil" class="h-16 w-16 rounded-full object-cover">
                @else
                    <span class="grid h-16 w-16 place-items-center rounded-full bg-brand-tint text-2xl text-brand">{{ \Illuminate\Support\Str::substr($student->name, 0, 1) }}</span>
                @endif
                <div class="flex-1">
                    <label for="photo" class="label">Foto profil</label>
                    <input id="photo" name="photo" type="file" accept="image/*" class="input">
                    <p class="hint">JPG atau PNG, maksimal 2 MB.</p>
                </div>
            </div>

            <dl class="grid grid-cols-2 gap-4 rounded-md bg-paper-2 px-4 py-3 text-sm">
                <div><dt class="text-muted">NIM</dt><dd class="font-mono">{{ $student->nim }}</dd></div>
                <div><dt class="text-muted">Jurusan</dt><dd>{{ $student->jurusan }}</dd></div>
            </dl>

            <div>
                <label for="name" class="label">Nama lengkap</label>
                <input id="name" name="name" value="{{ old('name', $student->name) }}" class="input" required autocomplete="name">
            </div>
            <div>
                <label for="email" class="label">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email', $student->email) }}" class="input" required autocomplete="email">
            </div>
            <fieldset>
                <legend class="label">Jenis kelamin</legend>
                <div class="flex gap-5 text-sm">
                    <label class="flex items-center gap-2"><input type="radio" name="gender" value="P" @checked(old('gender', $student->gender) === 'P') class="accent-brand"> Perempuan</label>
                    <label class="flex items-center gap-2"><input type="radio" name="gender" value="L" @checked(old('gender', $student->gender) === 'L') class="accent-brand"> Laki-laki</label>
                </div>
            </fieldset>

            <div class="border-t border-line pt-5">
                <p class="text-sm font-semibold">Ganti password</p>
                <p class="hint mt-0">Kosongkan kalau tidak ingin mengganti.</p>
                <div class="mt-3 grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="password" class="label">Password baru</label>
                        <input id="password" name="password" type="password" class="input" autocomplete="new-password" minlength="8">
                    </div>
                    <div>
                        <label for="password_confirmation" class="label">Ulangi password</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" class="input" autocomplete="new-password">
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-2">
                <a href="{{ route('profile') }}" class="btn-secondary">Batal</a>
                <button class="btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</x-layouts.app>
