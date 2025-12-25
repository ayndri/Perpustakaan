<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function index()
    {
        $student = Auth::guard('student')->user();
        $favorites = $student->favorites()->latest()->get();

        return view('favorites.index', compact('favorites'));
    }

    public function toggle($bookId)
    {
        $student = Auth::guard('student')->user();
        $book = Book::findOrFail($bookId);

        if ($student->favorites()->where('book_id', $bookId)->exists()) {
            $student->favorites()->detach($bookId);
            $message = 'Buku dihapus dari koleksi favorit.';
        } else {
            $student->favorites()->attach($bookId);
            $message = 'Buku ditambahkan ke koleksi favorit!';
        }

        return back()->with('success', $message);
    }
}
