<?php

namespace App\Http\Controllers;

use App\Models\Borrowing;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request, $bookId)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
        ]);

        $studentId = Auth::guard('student')->id();

        $hasBorrowed = Borrowing::where('student_id', $studentId)
            ->where('book_id', $bookId)
            ->where('status', 'returned')
            ->exists();

        if (!$hasBorrowed) {
            return back()->with('error', 'Anda harus meminjam dan membaca buku ini sebelum memberi ulasan.');
        }

        $existingReview = Review::where('student_id', $studentId)
            ->where('book_id', $bookId)
            ->exists();

        if ($existingReview) {
            return back()->with('error', 'Anda sudah memberikan ulasan untuk buku ini.');
        }

        Review::create([
            'student_id' => $studentId,
            'book_id' => $bookId,
            'rating' => $request->rating,
            'comment' => $request->comment
        ]);

        return back()->with('success', 'Terima kasih atas ulasan Anda!');
    }
}
