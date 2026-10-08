<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, Book $book)
    {
        $data = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
        ]);

        $student = auth('student')->user();

        $hasRead = $student->borrowings()->where('book_id', $book->id)->where('status', 'returned')->exists();
        if (! $hasRead) {
            return back()->with('error', 'Ulasan dibuka setelah kamu meminjam dan mengembalikan buku ini.');
        }

        if (Review::where('student_id', $student->id)->where('book_id', $book->id)->exists()) {
            return back()->with('error', 'Kamu sudah mengulas buku ini.');
        }

        Review::create($data + ['student_id' => $student->id, 'book_id' => $book->id]);

        return back()->with('success', 'Terima kasih, ulasanmu sudah tampil.');
    }
}
