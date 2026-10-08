<?php

namespace App\Providers;

use App\Models\Borrowing;
use App\Models\Category;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Paginator::defaultView('components.pagination');

        // Sidebar sisi mahasiswa: kategori dan penulis yang paling sering dipinjam.
        View::composer('components.layouts.app', function ($view) {
            $authors = Borrowing::query()
                ->join('books', 'books.id', '=', 'borrowings.book_id')
                ->whereNotNull('borrowings.handed_over_at')
                ->groupBy('books.author')
                ->selectRaw('books.author, count(*) as total')
                ->orderByDesc('total')
                ->limit(4)
                ->pluck('total', 'author');

            $student = auth('student')->user();

            $view->with('sidebar', [
                'categories' => Category::withCount('books')->orderBy('name')->get(),
                'authors' => $authors,
                'openLoans' => $student ? $student->borrowings()->open()->count() : 0,
            ]);
        });
        Carbon::setLocale(config('app.locale'));

        // Filesystem Vercel read-only kecuali /tmp, termasuk cache font dompdf
        if (env('VERCEL')) {
            config([
                'dompdf.options.font_cache' => '/tmp',
                'dompdf.options.temp_dir' => '/tmp',
            ]);
        }
    }
}
