<x-layouts.app title="Verifikasi KTM">
    <div class="mx-auto max-w-xl">
        <h1 class="text-3xl font-semibold">Verifikasi KTM</h1>
        <p class="mt-1 text-ink-2">Admin mencocokkan nama dan NIM di KTM dengan data akunmu. Setelah itu kamu bisa meminjam dan antre buku.</p>

        @switch($student->verification_status)
            @case('verified')
                <div class="card-pad mt-6 flex items-center gap-4">
                    <span class="grid h-11 w-11 place-items-center rounded-full bg-brand-tint text-brand"><x-icon name="check" /></span>
                    <div>
                        <p class="font-semibold">Akunmu sudah terverifikasi.</p>
                        <a href="{{ route('books.index') }}" class="text-sm font-semibold text-brand">Mulai pinjam buku</a>
                    </div>
                </div>
                @break

            @case('pending')
                <div class="card-pad mt-6 flex items-center gap-4">
                    <span class="grid h-11 w-11 place-items-center rounded-full bg-brass-tint text-brass"><x-icon name="clock" /></span>
                    <div>
                        <p class="font-semibold">KTM sedang diperiksa.</p>
                        <p class="text-sm text-muted">Dikirim {{ tanggal($student->updated_at, true) }}. Kamu bisa mengunggah ulang kalau fotonya kurang jelas.</p>
                    </div>
                </div>
                @break

            @case('rejected')
                <div class="mt-6 rounded-md border border-danger/25 bg-danger-tint px-5 py-4 text-danger">
                    <p class="font-semibold">Verifikasi ditolak</p>
                    <p class="text-sm">Alasan dari admin: {{ $student->rejection_reason }}</p>
                </div>
                @break
        @endswitch

        @unless ($student->isVerified())
            <form method="POST" action="{{ route('verification.store') }}" enctype="multipart/form-data" class="card-pad mt-6 space-y-4">
                @csrf
                <div>
                    <label for="ktm_image" class="label">Foto KTM</label>
                    <input id="ktm_image" name="ktm_image" type="file" accept="image/jpeg,image/png" class="input" required>
                    <p class="hint">JPG atau PNG, maksimal 2 MB. Pastikan nama dan NIM terbaca.</p>
                </div>
                <p class="flex items-start gap-2 rounded-md bg-paper-2 px-3 py-2.5 text-xs text-ink-2">
                    <x-icon name="info" :size="15" class="mt-px" />
                    Foto KTM disimpan privat. Tidak ada tautan publik ke file ini; hanya admin yang bisa membukanya lewat panel admin.
                </p>
                <button class="btn-primary">{{ $student->ktm_image ? 'Kirim ulang KTM' : 'Kirim KTM' }}</button>
            </form>
        @endunless
    </div>
</x-layouts.app>
