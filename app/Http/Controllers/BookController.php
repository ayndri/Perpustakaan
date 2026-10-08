<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use App\Services\Circulation;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('q'));

        $books = Book::with('category')
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->when($search !== '', fn ($q) => $q->where(fn ($q) => $q
                ->whereLike('title', "%{$search}%")
                ->orWhereLike('author', "%{$search}%")
                ->orWhereLike('isbn', "%{$search}%")))
            ->when($request->query('category'), fn ($q, $id) => $q->where('category_id', $id))
            ->when($request->query('available') === 'shelf', fn ($q) => $q->where('stock', '>', 0))
            ->when($request->query('available') === 'ebook', fn ($q) => $q->whereNotNull('digital_link'))
            ->orderBy('title')
            ->paginate(12)
            ->withQueryString();

        return view('books.index', [
            'books' => $books,
            'categories' => Category::orderBy('name')->get(),
            'search' => $search,
        ]);
    }

    public function show(Book $book, Circulation $circulation)
    {
        $book->load(['category', 'reviews.student'])->loadAvg('reviews', 'rating');

        $student = auth('student')->user();
        $mine = null;

        if ($student) {
            $reservation = $student->reservations()->where('book_id', $book->id)->where('status', 'waiting')->first();

            $mine = [
                'loan' => $student->borrowings()->open()->where('book_id', $book->id)->latest()->first(),
                'reservation' => $reservation,
                'position' => $reservation?->position(),
                'favorite' => $student->favorites()->where('book_id', $book->id)->exists(),
                'canReview' => $student->borrowings()->where('book_id', $book->id)->where('status', 'returned')->exists()
                    && ! $book->reviews->contains('student_id', $student->id),
            ];
        }

        $related = Book::where('category_id', $book->category_id)
            ->whereKeyNot($book->id)
            ->inRandomOrder()
            ->take(4)
            ->get();

        return view('books.show', [
            'book' => $book,
            'queue' => $circulation->queueLength($book->id),
            'mine' => $mine,
            'related' => $related,
        ]);
    }
}
