<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;

class HomeController extends Controller
{
    public function index()
    {
        $base = fn () => Book::query()->withAvg('reviews', 'rating')->withCount('reviews');

        $recentLoans = fn ($q) => $q
            ->whereNotNull('handed_over_at')
            ->where('handed_over_at', '>=', now()->subDays(90));

        // Rak hanya tampil kalau ada isinya; tidak ada angka atau rating yang dikarang.
        $shelves = collect([
            [
                'title' => 'Paling sering dipinjam',
                'caption' => '90 hari terakhir',
                'books' => $base()->withCount(['borrowings as recent_loans' => $recentLoans])
                    ->whereHas('borrowings', $recentLoans)
                    ->orderByDesc('recent_loans')->take(12)->get(),
            ],
            [
                'title' => 'Rating tertinggi',
                'caption' => 'dari ulasan mahasiswa yang sudah membaca',
                'books' => $base()->has('reviews')->orderByDesc('reviews_avg_rating')->orderByDesc('reviews_count')->take(12)->get(),
            ],
            [
                'title' => 'Baru masuk rak',
                'caption' => null,
                'books' => $base()->latest()->take(12)->get(),
            ],
        ]);

        $categoryShelves = Category::withCount('books')->orderByDesc('books_count')->take(2)->get()
            ->map(fn ($category) => [
                'title' => $category->name,
                'caption' => $category->books_count.' judul',
                'link' => route('books.index', ['category' => $category->id]),
                'books' => $base()->where('category_id', $category->id)->orderBy('title')->take(12)->get(),
            ]);

        return view('home', [
            'shelves' => $shelves->concat($categoryShelves)->filter(fn ($s) => $s['books']->isNotEmpty()),
            'totalBooks' => Book::count(),
            'ebookCount' => Book::whereNotNull('digital_link')->count(),
        ]);
    }
}
