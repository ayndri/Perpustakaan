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
