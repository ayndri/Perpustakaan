@props(['value', 'size' => 180])
<div {{ $attributes->merge(['class' => 'inline-block rounded-sm bg-white p-2 [&_svg]:block']) }} role="img" aria-label="Kode QR {{ $value }}">
    {!! \App\Support\Qr::svg($value, $size) !!}
</div>
