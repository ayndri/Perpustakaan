@props(['rating' => null, 'count' => null, 'size' => 13])
@if ($rating)
    <span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1']) }}>
        <span class="flex text-star" aria-hidden="true">
            @for ($i = 1; $i <= 5; $i++)
                <x-icon :name="$i <= round($rating) ? 'star-fill' : 'star'" :size="$size" />
            @endfor
        </span>
        <span class="text-xs font-semibold text-ink-2">{{ number_format($rating, 1, ',', '') }}</span>
        @if ($count)
            <span class="text-xs text-muted">({{ $count }})</span>
        @endif
        <span class="sr-only">Rating {{ number_format($rating, 1, ',', '') }} dari 5</span>
    </span>
@else
    <span {{ $attributes->merge(['class' => 'text-xs text-muted']) }}>Belum diulas</span>
@endif
