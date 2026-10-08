<?php

if (! function_exists('rupiah')) {
    function rupiah(int|float $amount): string
    {
        return 'Rp'.number_format($amount, 0, ',', '.');
    }
}

if (! function_exists('tanggal')) {
    /** "12 Okt 2026" atau, dengan jam, "12 Okt 2026, 14.30". */
    function tanggal(?\DateTimeInterface $date, bool $withTime = false): string
    {
        if (! $date) {
            return '–';
        }

        return \Illuminate\Support\Carbon::instance($date)->translatedFormat($withTime ? 'j M Y, H.i' : 'j M Y');
    }
}

if (! function_exists('asset_v')) {
    /** URL aset dengan penanda versi untuk cache-busting. Tidak gagal kalau file tidak terbaca dari fungsi. */
    function asset_v(string $path): string
    {
        $version = @filemtime(public_path($path)) ?: config('app.asset_version', '1');

        return asset($path).'?v='.$version;
    }
}
