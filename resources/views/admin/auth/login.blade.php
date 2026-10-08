<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk petugas · PerpusKampus</title>
    <meta name="robots" content="noindex">
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset_v('css/app.css') }}">
</head>
<body class="grid min-h-screen place-items-center px-4 py-10">
    <main class="w-full max-w-sm">
        <div class="flex items-center gap-2.5">
            <span class="grid h-9 w-9 place-items-center rounded-sm bg-brand text-white"><x-icon name="book" /></span>
            <span class="text-xl font-semibold">PerpusKampus</span>
        </div>
        <h1 class="mt-6 text-2xl font-semibold">Masuk petugas</h1>
        <p class="mt-1 text-sm text-muted">Panel ini untuk petugas meja layanan dan pengelola koleksi.</p>

        <div class="mt-5 space-y-3"><x-flash /></div>

        <form method="POST" action="{{ route('admin.login') }}" class="card-pad mt-4 space-y-4">
            @csrf
            <div>
                <label for="email" class="label">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" class="input" required autofocus autocomplete="username">
            </div>
            <div>
                <label for="password" class="label">Password</label>
                <input id="password" name="password" type="password" class="input" required autocomplete="current-password">
            </div>
            <button class="btn-primary w-full">Masuk</button>
        </form>

        <p class="mt-6 rounded-md border border-dashed border-line-strong px-4 py-3 text-xs text-ink-2">
            Akun demo: <span class="font-mono">admin@perpus.test</span> · <span class="font-mono">password123</span>
        </p>
        <p class="mt-4 text-center text-sm"><a href="{{ route('home') }}" class="text-muted">Kembali ke katalog</a></p>
    </main>
</body>
</html>
