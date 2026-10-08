<x-layouts.app title="Tidak ditemukan">
    <div class="mx-auto max-w-md py-10 text-center">
        <p class="font-mono text-sm text-muted">404</p>
        <h1 class="mt-2 text-3xl font-semibold">Halaman ini tidak ada di rak mana pun</h1>
        <p class="mt-2 text-ink-2">Mungkin tautannya salah ketik, atau datanya sudah dihapus.</p>
        <a href="{{ route('books.index') }}" class="btn-primary mt-6">Ke katalog</a>
    </div>
</x-layouts.app>
