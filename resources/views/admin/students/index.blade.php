<x-layouts.admin title="Anggota">
    <div class="max-w-6xl">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-3xl font-semibold">Anggota</h1>
                <p class="mt-1 text-sm text-muted">{{ $students->total() }} mahasiswa</p>
            </div>
            <a href="{{ route('admin.students.create') }}" class="btn-primary"><x-icon name="plus" :size="16" /> Daftarkan anggota</a>
        </div>

        <form method="GET" class="mt-5 flex flex-wrap gap-2" role="search">
            <label for="q" class="sr-only">Cari</label>
            <input id="q" name="q" value="{{ $search }}" class="input w-72" placeholder="Nama, NIM, atau email">
            <label for="jurusan" class="sr-only">Program studi</label>
            <select id="jurusan" name="jurusan" class="input w-56">
                <option value="">Semua program studi</option>
                @foreach ($majors as $major)
                    <option value="{{ $major }}" @selected(request('jurusan') === $major)>{{ $major }}</option>
                @endforeach
            </select>
            <button class="btn-secondary">Terapkan</button>
        </form>

        @if ($students->isEmpty())
            <x-empty class="mt-6" title="Tidak ada anggota yang cocok" icon="users" />
        @else
            <div class="table-wrap mt-5">
                <table class="table">
                    <thead><tr><th>Nama</th><th>NIM</th><th>Program studi</th><th>Status</th><th class="text-right">Dipinjam</th><th></th></tr></thead>
                    <tbody>
                        @foreach ($students as $student)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.students.show', $student) }}" class="font-medium text-ink">{{ $student->name }}</a>
                                    <span class="block text-xs text-muted">{{ $student->email }}</span>
                                </td>
                                <td class="font-mono text-xs">{{ $student->nim }}</td>
                                <td class="text-ink-2">{{ $student->jurusan }}</td>
                                <td>
                                    @switch($student->verification_status)
                                        @case('verified') <span class="badge-brand">Terverifikasi</span> @break
                                        @case('pending') <span class="badge-brass">Menunggu cek KTM</span> @break
                                        @case('rejected') <span class="badge-danger">KTM ditolak</span> @break
                                        @default <span class="badge-neutral">Belum unggah KTM</span>
                                    @endswitch
                                </td>
                                <td class="text-right">{{ $student->open_loans ?: '–' }}</td>
                                <td class="whitespace-nowrap text-right">
                                    <a href="{{ route('admin.students.card', $student) }}" target="_blank" class="btn-ghost btn-sm"><x-icon name="id" :size="14" /> Kartu</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-5">{{ $students->links() }}</div>
        @endif
    </div>
</x-layouts.admin>
