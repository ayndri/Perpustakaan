<?php

namespace App\Http\Middleware;

use App\Services\Circulation;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Pengganti cron: sebelum halaman membaca stok, tiket yang lewat batas ambil dan
 * e-book yang lewat masa pinjam dilepas dulu. Vercel Hobby hanya mengizinkan cron
 * harian, sedangkan tiket bisa kedaluwarsa kapan saja.
 */
class SweepCirculation
{
    public function __construct(private Circulation $circulation) {}

    public function handle(Request $request, Closure $next): Response
    {
        $this->circulation->sweep();

        return $next($request);
    }
}
