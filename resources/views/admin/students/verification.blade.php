<x-layouts.admin title="Verifikasi KTM">
    <div class="max-w-5xl">
        <h1 class="text-3xl font-semibold">Verifikasi KTM</h1>
        <p class="mt-1 text-ink-2">Cocokkan nama dan NIM di foto KTM dengan data akun. Yang paling lama menunggu ada di atas.</p>

        @forelse ($students as $student)
            <article class="card mt-5 grid gap-5 p-5 md:grid-cols-[minmax(0,22rem)_1fr]">
                <a href="{{ route('admin.students.ktm', $student) }}" target="_blank" rel="noopener" class="block overflow-hidden rounded-md border border-line bg-paper-2">
                    <img src="{{ route('admin.students.ktm', $student) }}" alt="Foto KTM {{ $student->name }}" class="max-h-64 w-full object-contain" loading="lazy">
                </a>
                <div class="flex flex-col">
                    <dl class="grid grid-cols-2 gap-x-6 gap-y-3 text-sm">
                        <div class="col-span-2"><dt class="text-muted">Nama di akun</dt><dd class="text-lg font-semibold">{{ $student->name }}</dd></div>
                        <div><dt class="text-muted">NIM di akun</dt><dd class="font-mono text-base">{{ $student->nim }}</dd></div>
                        <div><dt class="text-muted">Program studi</dt><dd>{{ $student->jurusan }}</dd></div>
                        <div class="col-span-2"><dt class="text-muted">Dikirim</dt><dd>{{ tanggal($student->updated_at, true) }}</dd></div>
                    </dl>

                    <div class="mt-auto flex flex-wrap items-start gap-2 pt-5">
                        <form method="POST" action="{{ route('admin.students.approve', $student) }}">
                            @csrf
                            <button class="btn-primary"><x-icon name="check" :size="16" /> Cocok, verifikasi</button>
                        </form>
                        <details class="group">
                            <summary class="btn-danger cursor-pointer list-none [&::-webkit-details-marker]:hidden">Tolak</summary>
                            <form method="POST" action="{{ route('admin.students.reject', $student) }}" class="mt-2 flex gap-2">
                                @csrf
                                <label for="reason-{{ $student->id }}" class="sr-only">Alasan penolakan</label>
                                <input id="reason-{{ $student->id }}" name="reason" class="input w-64" required placeholder="Contoh: foto buram, NIM tidak terbaca">
                                <button class="btn-danger shrink-0">Kirim</button>
                            </form>
                        </details>
                    </div>
                </div>
            </article>
        @empty
            <x-empty class="mt-6" title="Tidak ada KTM yang menunggu" icon="id">
                KTM yang diunggah mahasiswa akan muncul di sini.
            </x-empty>
        @endforelse
    </div>
</x-layouts.admin>
