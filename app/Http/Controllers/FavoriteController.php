<?php

namespace App\Http\Controllers;

use App\Models\Book;

class FavoriteController extends Controller
{
    public function index()
    {
        return view('favorites.index', [
            'favorites' => auth('student')->user()->favorites()->with('category')->withAvg('reviews', 'rating')->withCount('reviews')->latest('favorites.created_at')->get(),
        ]);
    }

    public function toggle(Book $book)
    {
        $result = auth('student')->user()->favorites()->toggle($book->id);

        return back()->with('success', $result['attached']
            ? 'Disimpan ke daftar bacaan.'
            : 'Dihapus dari daftar bacaan.');
    }
}
