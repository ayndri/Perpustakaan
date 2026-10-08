@props(['title' => null])
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ? $title.' · ' : '' }}PerpusKampus</title>
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}">
</head>
{{-- Halaman masuk/daftar tanpa sidebar dan bilah atas: satu tugas, tanpa gangguan navigasi. --}}
<body class="flex min-h-screen flex-col bg-paper-2">
    <header class="px-4 py-5 sm:px-8">
        <a href="{{ route('home') }}" class="inline-flex items-center gap-2.5 no-underline">
            <span class="grid h-8 w-8 place-items-center rounded-md bg-brand text-white"><x-icon name="book" :size="17" /></span>
            <span class="text-[15px] font-extrabold tracking-tight text-ink">PERPUS<span class="font-medium text-ink-2">KAMPUS</span></span>
        </a>
    </header>

    <main class="flex flex-1 items-start justify-center px-4 pb-12 pt-4 sm:items-center sm:pt-0">
        <div class="w-full {{ $attributes->get('class', 'max-w-sm') }}">
            @if (session('success') || session('error') || (isset($errors) && $errors->any()))
                <div class="mb-5 space-y-3"><x-flash /></div>
            @endif
            {{ $slot }}
        </div>
    </main>

    <footer class="px-4 pb-6 text-center text-xs text-muted sm:px-8">
        <a href="{{ route('books.index') }}" class="text-muted">Lihat katalog tanpa masuk</a>
    </footer>
</body>
</html>
