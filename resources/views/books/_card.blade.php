<article class="group relative">
    <a href="{{ route('books.show', $book) }}" class="block no-underline">
        <x-book-cover :book="$book" :width="300" class="shadow-md shadow-ink/10 transition-transform duration-200 group-hover:-translate-y-1" />
        <h3 class="mt-3 line-clamp-2 text-sm font-semibold leading-snug text-ink group-hover:text-brand">{{ $book->title }}</h3>
        <p class="mt-0.5 truncate text-xs text-muted">{{ $book->author }}</p>
    </a>
    <x-stars class="mt-1.5" :rating="$book->reviews_avg_rating" :count="$book->reviews_count" :size="12" />
    <div class="mt-1.5 flex flex-wrap items-center gap-1.5">
        @if ($book->stock > 0)
            <span class="badge-brand">{{ $book->stock }} di rak</span>
        @else
            <span class="badge-neutral">Tidak ada di rak</span>
        @endif
        @if ($book->digital_link)
            <span class="badge-neutral">E-book</span>
        @endif
    </div>
</article>
