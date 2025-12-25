<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;

class BookController extends Controller
{
    private $books = [
        [
            'id' => 1,
            'title' => 'Belajar Laravel untuk Pemula',
            'author' => 'Budi Santoso',
            'year' => 2023,
            'category' => 'Pemrograman'
        ],
        [
            'id' => 2,
            'title' => 'Algoritma dan Struktur Data',
            'author' => 'Rina Kurnia',
            'year' => 2022,
            'category' => 'Akademik'
        ],
        [
            'id' => 3,
            'title' => 'Sistem Basis Data',
            'author' => 'Andi Wijaya',
            'year' => 2021,
            'category' => 'Teknologi'
        ]
    ];

    // public function index()
    // {
    //     return view('books.index', ['books' => $this->books]);
    // }

    // public function show($id)
    // {
    //     $book = collect($this->books)->firstWhere('id', $id);

    //     if (!$book) {
    //         abort(404);
    //     }

    //     return view('books.show', ['book' => $book]);
    // }

    public function index(Request $request)
    {
        $query = Book::with('category');

        if ($request->has('search') && $request->search != '') {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->has('category') && $request->category != '') {
            $query->where('category_id', $request->category);
        }
        $books = $query->latest()->paginate(8);
        $categories = Category::all();

        return view('books.index', compact('books', 'categories'));
    }

    public function show($id)
    {
        $book = Book::with('category')->findOrFail($id);
        return view('books.show', compact('book'));
    }
}
