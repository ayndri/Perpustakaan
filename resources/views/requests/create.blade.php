<x-layouts.app title="Usulkan buku">
    <div class="mx-auto max-w-xl">
        <a href="{{ route('student.requests.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-muted no-underline hover:text-ink">
            <x-icon name="arrow-left" :size="16" /> Usulan buku
        </a>
        <h1 class="mt-4 text-3xl font-semibold">Usulkan judul</h1>
        <p class="mt-1 text-sm text-muted">Makin jelas alasannya (untuk mata kuliah apa, berapa orang butuh), makin mudah admin memprioritaskan.</p>

        <form method="POST" action="{{ route('student.requests.store') }}" enctype="multipart/form-data" class="card-pad mt-6 space-y-5">
            @csrf
            <div>
                <label for="title" class="label">Judul buku</label>
                <input id="title" name="title" value="{{ old('title') }}" class="input" required>
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label for="author" class="label">Penulis</label>
                    <input id="author" name="author" value="{{ old('author') }}" class="input" required>
                </div>
                <div>
                    <label for="publisher" class="label">Penerbit <span class="font-normal text-muted">(opsional)</span></label>
                    <input id="publisher" name="publisher" value="{{ old('publisher') }}" class="input">
                </div>
            </div>
            <div>
                <label for="category" class="label">Bidang <span class="font-normal text-muted">(opsional)</span></label>
                <input id="category" name="category" value="{{ old('category') }}" class="input" placeholder="Contoh: Basis data">
            </div>
            <div>
                <label for="reason" class="label">Kenapa perlu dibeli?</label>
                <textarea id="reason" name="reason" rows="4" class="input" required minlength="10" maxlength="1000" placeholder="Contoh: referensi utama mata kuliah Basis Data Lanjut semester ini">{{ old('reason') }}</textarea>
            </div>
            <div>
                <label for="image" class="label">Foto sampul <span class="font-normal text-muted">(opsional)</span></label>
                <input id="image" name="image" type="file" accept="image/jpeg,image/png" class="input">
                <p class="hint">Membantu admin menemukan edisi yang tepat.</p>
            </div>
            <div class="flex justify-end gap-2">
                <a href="{{ route('student.requests.index') }}" class="btn-secondary">Batal</a>
                <button class="btn-primary">Kirim usulan</button>
            </div>
        </form>
    </div>
</x-layouts.app>
