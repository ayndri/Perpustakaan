<x-layouts.app title="Sesi berakhir">
    <div class="mx-auto max-w-md py-10 text-center">
        <p class="font-mono text-sm text-muted">419</p>
        <h1 class="mt-2 text-3xl font-semibold">Sesimu sudah kedaluwarsa</h1>
        <p class="mt-2 text-ink-2">Halaman terlalu lama terbuka, jadi formulirnya tidak dikirim. Muat ulang lalu coba sekali lagi.</p>
        <a href="{{ url()->previous() }}" class="btn-primary mt-6">Kembali</a>
    </div>
</x-layouts.app>
