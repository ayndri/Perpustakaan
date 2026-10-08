@props(['title', 'icon' => 'inbox'])
<div {{ $attributes->merge(['class' => 'rounded-md border border-dashed border-line-strong px-6 py-10 text-center']) }}>
    <x-icon :name="$icon" :size="28" class="mx-auto text-muted" />
    <p class="mt-3 text-lg text-ink">{{ $title }}</p>
    @if (trim($slot) !== '')
        <div class="mx-auto mt-1 max-w-md text-sm text-muted">{{ $slot }}</div>
    @endif
    @isset($action)
        <div class="mt-4">{{ $action }}</div>
    @endisset
</div>
