<x-layouts.admin title="Laporan">
    <div class="max-w-4xl">
        <h1 class="text-3xl font-semibold">Laporan</h1>
        <p class="mt-1 text-ink-2">Semua laporan dibuat sebagai PDF dan terbuka di tab baru.</p>

        <div class="mt-6 grid gap-5 md:grid-cols-2">
            <form method="GET" action="{{ route('admin.reports.borrowings') }}" target="_blank" class="card-pad space-y-4">
                <div>
                    <h2 class="text-xl font-semibold">Peminjaman</h2>
                    <p class="text-sm text-muted">Buku yang diserahkan dalam rentang tanggal, lengkap dengan status kembali dan denda.</p>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="start_date" class="label">Dari</label>
                        <input id="start_date" name="start_date" type="date" value="{{ now()->startOfMonth()->toDateString() }}" class="input" required>
                    </div>
                    <div>
                        <label for="end_date" class="label">Sampai</label>
                        <input id="end_date" name="end_date" type="date" value="{{ now()->toDateString() }}" class="input" required>
                    </div>
                </div>
                <button class="btn-primary"><x-icon name="file" :size="16" /> Buat PDF</button>
            </form>

            <form method="GET" action="{{ route('admin.reports.members') }}" target="_blank" class="card-pad space-y-4">
                <div>
                    <h2 class="text-xl font-semibold">Anggota</h2>
                    <p class="text-sm text-muted">Daftar anggota beserta status verifikasinya.</p>
                </div>
                <div>
                    <label for="jurusan" class="label">Program studi</label>
                    <select id="jurusan" name="jurusan" class="input">
                        <option value="">Semua program studi</option>
                        @foreach ($majors as $major)
                            <option value="{{ $major }}">{{ $major }}</option>
                        @endforeach
                    </select>
                </div>
                <button class="btn-primary"><x-icon name="file" :size="16" /> Buat PDF</button>
            </form>
        </div>

        <p class="mt-6 text-sm text-muted">Kartu anggota dengan QR dicetak per orang dari halaman <a href="{{ route('admin.students.index') }}" class="font-semibold text-brand">Anggota</a>.</p>
    </div>
</x-layouts.admin>
