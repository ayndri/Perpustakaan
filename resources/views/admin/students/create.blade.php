<x-layouts.admin title="Daftarkan anggota">
    <div class="max-w-xl">
        <a href="{{ route('admin.students.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-muted no-underline hover:text-ink">
            <x-icon name="arrow-left" :size="16" /> Anggota
        </a>
        <h1 class="mt-4 text-3xl font-semibold">Daftarkan anggota</h1>
        <p class="mt-1 text-sm text-muted">Untuk mahasiswa yang mendaftar langsung di meja dengan menunjukkan KTM. Akun langsung terverifikasi, dan password sementara ditampilkan sekali setelah disimpan.</p>

        <form method="POST" action="{{ route('admin.students.store') }}" class="card-pad mt-6 space-y-4">
            @csrf
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="nim" class="label">NIM</label>
                    <input id="nim" name="nim" value="{{ old('nim') }}" class="input font-mono" required>
                </div>
                <div>
                    <label for="jurusan" class="label">Program studi</label>
                    <input id="jurusan" name="jurusan" value="{{ old('jurusan') }}" class="input" required>
                </div>
            </div>
            <div>
                <label for="name" class="label">Nama lengkap</label>
                <input id="name" name="name" value="{{ old('name') }}" class="input" required>
            </div>
            <div>
                <label for="email" class="label">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" class="input" required>
            </div>
            <fieldset>
                <legend class="label">Jenis kelamin</legend>
                <div class="flex gap-5 text-sm">
                    <label class="flex items-center gap-2"><input type="radio" name="gender" value="P" @checked(old('gender') === 'P') class="accent-brand" required> Perempuan</label>
                    <label class="flex items-center gap-2"><input type="radio" name="gender" value="L" @checked(old('gender') === 'L') class="accent-brand"> Laki-laki</label>
                </div>
            </fieldset>
            <div class="flex justify-end gap-2">
                <a href="{{ route('admin.students.index') }}" class="btn-secondary">Batal</a>
                <button class="btn-primary">Daftarkan</button>
            </div>
        </form>
    </div>
</x-layouts.admin>
