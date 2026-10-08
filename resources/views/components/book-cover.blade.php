@props(['book', 'width' => 400])
@php($url = $book->coverUrl($width))
<div {{ $attributes->merge(['class' => 'relative aspect-[2/3] overflow-hidden rounded-sm border border-ink/10 bg-paper-2']) }}>
    @if ($url)
        <img src="{{ $url }}" alt="Sampul {{ $book->title }}" loading="lazy" decoding="async"
             class="absolute inset-0 h-full w-full object-cover"
             onerror="this.remove()">
    @endif
    {{-- Cadangan tipografis kalau sampul tidak ada atau gagal dimuat (gambar dihapus oleh onerror). --}}
    <div class="flex h-full flex-col justify-between bg-brand p-3 text-brand-soft">
        <span class="text-sm leading-snug text-white">{{ \Illuminate\Support\Str::limit($book->title, 60) }}</span>
        <span class="text-[11px]">{{ \Illuminate\Support\Str::limit($book->author, 30) }}</span>
    </div>
</div>
