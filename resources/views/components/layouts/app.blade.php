@props(['title' => null, 'wide' => false])
@php
    $student = auth('student')->user();
    $menu = [
        ['home', 'Beranda', 'home', 'home', true],
        ['books.index', 'Katalog', 'books', 'books.*', true],
        ['profile', 'Pinjamanku', 'book', 'profile|borrowings.*', false],
        ['favorites.index', 'Daftar baca', 'bookmark', 'favorites.*', false],
        ['student.requests.index', 'Usulan buku', 'lightbulb', 'student.requests.*', false],
    ];
    $currentCategory = request()->routeIs('books.index') ? request('category') : null;
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ? $title.' · ' : '' }}PerpusKampus</title>
    <meta name="description" content="Katalog dan layanan peminjaman perpustakaan kampus: pinjam buku fisik dengan tiket QR, baca e-book, dan antre buku yang sedang dipinjam.">
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}">
</head>
<body>
    <a href="#konten" class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-50 btn-primary">Lompat ke konten</a>

    <div class="lg:grid lg:min-h-screen lg:grid-cols-[16rem_1fr]">
        {{-- Sidebar: menu, rak kategori, dan penulis populer. Di layar kecil jadi laci. --}}
        <div id="sidebar-backdrop" class="fixed inset-0 z-30 hidden bg-ink/30 lg:hidden" onclick="toggleSidebar(false)"></div>
        <aside id="sidebar"
               class="fixed inset-y-0 left-0 z-40 no-scrollbar flex w-64 -translate-x-full flex-col overflow-y-auto border-r border-line bg-paper-2 transition-transform duration-200 lg:sticky lg:top-0 lg:h-screen lg:translate-x-0">
            <div class="flex h-16 shrink-0 items-center justify-between px-5">
                <a href="{{ route('home') }}" class="flex items-center gap-2.5 no-underline">
                    <span class="grid h-8 w-8 place-items-center rounded-md bg-brand text-white"><x-icon name="book" :size="17" /></span>
                    <span class="text-[15px] font-extrabold tracking-tight text-ink">PERPUS<span class="font-medium text-ink-2">KAMPUS</span></span>
                </a>
                <button type="button" class="btn-ghost btn-sm lg:hidden" onclick="toggleSidebar(false)">
                    <x-icon name="x" /><span class="sr-only">Tutup menu</span>
                </button>
            </div>

            <nav class="px-3" aria-label="Menu utama">
                <p class="px-3 pb-1.5 pt-2 text-xs font-bold text-ink">Menu</p>
                @foreach ($menu as [$route, $label, $icon, $pattern, $public])
                    @continue(! $public && ! $student)
                    @php($active = request()->routeIs(...explode('|', $pattern)) && ! $currentCategory)
                    <a href="{{ route($route) }}" class="{{ $active ? 'nav-link-active' : 'nav-link' }} flex items-center gap-3" @if ($active) aria-current="page" @endif>
                        <x-icon :name="$icon" :size="17" />
                        <span class="flex-1">{{ $label }}</span>
                        @if ($route === 'profile' && $sidebar['openLoans'] > 0)
                            <span class="rounded-full bg-brand px-1.5 text-[11px] font-bold text-white">{{ $sidebar['openLoans'] }}</span>
                        @endif
                    </a>
                @endforeach
            </nav>

            <nav class="mt-5 px-3" aria-label="Kategori">
                <p class="px-3 pb-1.5 text-xs font-bold text-ink">Kategori</p>
                @foreach ($sidebar['categories'] as $i => $category)
                    @php($active = (string) $currentCategory === (string) $category->id)
                    <a href="{{ route('books.index', ['category' => $category->id]) }}"
                       class="{{ $active ? 'nav-link-active' : 'nav-link' }} flex items-center justify-between py-1.5 {{ $i >= 6 ? 'hidden more-category' : '' }}"
                       @if ($active) aria-current="page" @endif>
                        <span class="truncate">{{ $category->name }}</span>
                        <span class="text-xs text-muted">{{ $category->books_count }}</span>
                    </a>
                @endforeach
                @if ($sidebar['categories']->count() > 6)
                    <button type="button" class="nav-link flex w-full items-center justify-between py-1.5 text-muted"
                            onclick="document.querySelectorAll('.more-category').forEach(e => e.classList.toggle('hidden')); const s = this.querySelector('span'); s.textContent = s.textContent === 'Lihat semua' ? 'Lebih sedikit' : 'Lihat semua'">
                        <span>Lihat semua</span>
                    </button>
                @endif
            </nav>

            @if ($sidebar['authors']->isNotEmpty())
                <div class="mt-5 px-3">
                    <p class="px-3 pb-2 text-xs font-bold text-ink">Penulis paling dicari</p>
                    <ul class="space-y-0.5">
                        @foreach ($sidebar['authors'] as $author => $total)
                            <li>
                                <a href="{{ route('books.index', ['q' => $author]) }}" class="nav-link flex items-center gap-3 py-1.5">
                                    <span class="grid h-8 w-8 shrink-0 place-items-center rounded-full bg-brand-tint text-xs font-bold text-brand">
                                        {{ \Illuminate\Support\Str::of($author)->explode(' ')->take(2)->map(fn ($w) => \Illuminate\Support\Str::substr($w, 0, 1))->join('') }}
                                    </span>
                                    <span class="min-w-0">
                                        <span class="block truncate">{{ $author }}</span>
                                        <span class="block text-[11px] text-muted">{{ $total }}× dipinjam</span>
                                    </span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="mt-auto border-t border-line px-6 py-4 text-[11px] leading-relaxed text-muted">
                Pinjam {{ config('library.loan_days') }} hari · perpanjang {{ config('library.max_renewals') }}× · denda {{ rupiah(config('library.fine_per_day')) }}/hari.
                <br>Proyek portofolio; anggota dan riwayat pinjam adalah data demo.
            </div>
        </aside>

        <div class="flex min-w-0 flex-col">
            <header class="sticky top-0 z-20 border-b border-line bg-paper">
                <div class="flex h-16 items-center gap-3 px-4 sm:px-8">
                    <button type="button" class="btn-ghost btn-sm -ml-2 lg:hidden" onclick="toggleSidebar(true)" aria-controls="sidebar">
                        <x-icon name="menu" /><span class="sr-only">Buka menu</span>
                    </button>

                    <form action="{{ route('books.index') }}" method="GET" class="relative w-full max-w-md" role="search">
                        <label for="cari" class="sr-only">Cari buku</label>
                        <x-icon name="search" :size="17" class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-muted" />
                        <input id="cari" name="q" type="search" value="{{ request()->routeIs('books.index') ? request('q') : '' }}"
                               class="input h-10 rounded-full border-transparent bg-paper-2 py-2 pl-10 focus:bg-paper" placeholder="Cari judul, penulis, atau ISBN">
                    </form>

                    <div class="ml-auto flex shrink-0 items-center gap-2">
                        @if ($student)
                            <details class="relative">
                                <summary class="flex cursor-pointer list-none items-center gap-2.5 rounded-full p-1 hover:bg-paper-2 sm:pr-3 [&::-webkit-details-marker]:hidden">
                                    <span class="relative">
                                        @if ($student->photoUrl())
                                            <img src="{{ $student->photoUrl() }}" alt="" class="h-9 w-9 rounded-full object-cover">
                                        @else
                                            <span class="grid h-9 w-9 place-items-center rounded-full bg-brand text-sm font-bold text-white">{{ \Illuminate\Support\Str::substr($student->name, 0, 1) }}</span>
                                        @endif
                                        @if ($student->unreadNotifications()->exists())
                                            <span class="absolute -right-0.5 -top-0.5 h-3 w-3 rounded-full border-2 border-paper bg-brass"></span>
                                            <span class="sr-only">Ada pemberitahuan baru</span>
                                        @endif
                                    </span>
                                    <span class="hidden text-sm font-semibold sm:inline">{{ $student->name }}</span>
                                </summary>
                                <div class="absolute right-0 z-30 mt-2 w-56 rounded-md border border-line bg-surface p-1.5 shadow-lg shadow-ink/10">
                                    <div class="border-b border-line px-3 pb-2 pt-1.5">
                                        <p class="truncate text-sm font-semibold">{{ $student->name }}</p>
                                        <p class="font-mono text-xs text-muted">{{ $student->nim }}</p>
                                    </div>
                                    <a href="{{ route('profile') }}" class="nav-link flex">Pinjamanku</a>
                                    <a href="{{ route('profile.edit') }}" class="nav-link flex">Pengaturan akun</a>
                                    @unless ($student->isVerified())
                                        <a href="{{ route('verification.index') }}" class="nav-link flex text-brass">Verifikasi KTM</a>
                                    @endunless
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button class="nav-link flex w-full text-left">Keluar</button>
                                    </form>
                                </div>
                            </details>
                        @else
                            <a href="{{ route('login') }}" class="nav-link">Masuk</a>
                            <a href="{{ route('register') }}" class="btn-primary hidden sm:inline-flex">Daftar anggota</a>
                        @endif
                    </div>
                </div>
            </header>

            @if ($student && ! $student->isVerified() && ! request()->routeIs('verification.*'))
                <div class="border-b border-brass/20 bg-brass-tint">
                    <div class="flex flex-wrap items-center gap-x-3 gap-y-1 px-4 py-2.5 text-sm text-brass sm:px-8">
                        <x-icon name="id" :size="16" />
                        @if ($student->verification_status === 'pending')
                            <span>KTM-mu sedang diperiksa admin. Kamu sudah bisa menelusuri katalog sambil menunggu.</span>
                        @elseif ($student->verification_status === 'rejected')
                            <span>Verifikasi KTM ditolak: {{ $student->rejection_reason }}.</span>
                            <a href="{{ route('verification.index') }}" class="font-semibold underline">Unggah ulang</a>
                        @else
                            <span>Unggah KTM dulu supaya bisa meminjam.</span>
                            <a href="{{ route('verification.index') }}" class="font-semibold underline">Verifikasi sekarang</a>
                        @endif
                    </div>
                </div>
            @endif

            <main id="konten" class="w-full max-w-6xl flex-1 px-4 py-6 sm:px-8 sm:py-8">
                @if (session('success') || session('error') || (isset($errors) && $errors->any()))
                    <div class="mb-6 max-w-3xl space-y-3"><x-flash /></div>
                @endif
                {{ $slot }}
            </main>
        </div>
    </div>

    <script>
        function toggleSidebar(open) {
            document.getElementById('sidebar').classList.toggle('-translate-x-full', !open);
            document.getElementById('sidebar-backdrop').classList.toggle('hidden', !open);
        }
        document.addEventListener('keydown', (e) => { if (e.key === 'Escape') toggleSidebar(false); });
    </script>
</body>
</html>
