@props(['title' => null])
@php
    $nav = [
        ['admin.desk', 'Meja layanan', 'scan', 'admin.desk*'],
        ['admin.dashboard', 'Hari ini', 'home', 'admin.dashboard'],
        ['admin.borrowings.index', 'Peminjaman', 'books', 'admin.borrowings.*'],
        ['admin.reservations.index', 'Antrean', 'queue', 'admin.reservations.*'],
        ['admin.books.index', 'Koleksi', 'book', 'admin.books.*'],
        ['admin.students.index', 'Anggota', 'users', 'admin.students.index|admin.students.show|admin.students.create'],
        ['admin.students.verification', 'Verifikasi KTM', 'id', 'admin.students.verification'],
        ['admin.requests.index', 'Usulan buku', 'lightbulb', 'admin.requests.*'],
        ['admin.reports.index', 'Laporan', 'file', 'admin.reports.*'],
    ];
    $pendingKtm = \App\Models\Student::where('verification_status', 'pending')->count();
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ? $title.' · ' : '' }}Admin PerpusKampus</title>
    <meta name="robots" content="noindex">
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}">
    {{ $head ?? '' }}
</head>
<body>
    <a href="#konten" class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-50 btn-primary">Lompat ke konten</a>
    <div class="lg:grid lg:min-h-screen lg:grid-cols-[15rem_1fr]">
        <aside class="border-b border-line bg-paper-2/60 lg:border-b-0 lg:border-r">
            <div class="flex h-14 items-center justify-between px-4 lg:h-16">
                <a href="{{ route('admin.desk') }}" class="flex items-center gap-2.5 no-underline">
                    <span class="grid h-8 w-8 place-items-center rounded-sm bg-brand text-white"><x-icon name="book" :size="17" /></span>
                    <span class="leading-tight">
                        <span class="block font-semibold text-ink">PerpusKampus</span>
                        <span class="block text-[11px] text-muted">Panel petugas</span>
                    </span>
                </a>
                <button type="button" class="btn-ghost btn-sm lg:hidden" aria-controls="admin-nav" aria-expanded="false"
                        onclick="const n=document.getElementById('admin-nav');n.classList.toggle('hidden');this.setAttribute('aria-expanded',!n.classList.contains('hidden'))">
                    <x-icon name="menu" /><span class="sr-only">Menu</span>
                </button>
            </div>
            <nav id="admin-nav" class="hidden space-y-0.5 px-3 pb-4 lg:block" aria-label="Admin">
                @foreach ($nav as [$route, $label, $icon, $pattern])
                    @php($active = request()->routeIs(...explode('|', $pattern)))
                    <a href="{{ route($route) }}" class="{{ $active ? 'nav-link-active' : 'nav-link' }} flex items-center gap-2.5" @if ($active) aria-current="page" @endif>
                        <x-icon :name="$icon" :size="17" />
                        <span class="flex-1">{{ $label }}</span>
                        @if ($route === 'admin.students.verification' && $pendingKtm > 0)
                            <span class="badge-brass">{{ $pendingKtm }}</span>
                        @endif
                    </a>
                @endforeach
                <div class="mt-4 border-t border-line pt-4">
                    <p class="px-3 text-xs text-muted">Masuk sebagai</p>
                    <p class="px-3 text-sm font-semibold">{{ auth()->user()->name }}</p>
                    <div class="mt-2 flex gap-1 px-1">
                        <a href="{{ route('home') }}" class="nav-link text-xs">Lihat situs</a>
                        <form method="POST" action="{{ route('admin.logout') }}">
                            @csrf
                            <button class="nav-link text-xs">Keluar</button>
                        </form>
                    </div>
                </div>
            </nav>
        </aside>

        <main id="konten" class="min-w-0 px-4 py-6 sm:px-8 sm:py-8">
            @if (session('success') || session('error') || (isset($errors) && $errors->any()))
                <div class="mb-6 max-w-3xl space-y-3"><x-flash /></div>
            @endif
            {{ $slot }}
        </main>
    </div>
    {{ $scripts ?? '' }}
</body>
</html>
