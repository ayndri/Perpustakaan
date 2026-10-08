@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Halaman" class="flex flex-wrap items-center justify-between gap-3 text-sm">
        <p class="text-muted">
            {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} dari {{ $paginator->total() }}
        </p>
        <div class="flex items-center gap-1">
            @if ($paginator->onFirstPage())
                <span class="btn-secondary btn-sm opacity-50" aria-disabled="true">Sebelumnya</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="btn-secondary btn-sm no-underline">Sebelumnya</a>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="px-2 text-muted">{{ $element }}</span>
                @endif
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page" class="btn btn-sm bg-brand text-white">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="btn-ghost btn-sm hidden no-underline sm:inline-flex">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="btn-secondary btn-sm no-underline">Berikutnya</a>
            @else
                <span class="btn-secondary btn-sm opacity-50" aria-disabled="true">Berikutnya</span>
            @endif
        </div>
    </nav>
@endif
