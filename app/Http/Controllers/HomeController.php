<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $topBooks = Book::with('category')->latest()->take(4)->get();
        $featuredBooks = Book::with('category')->inRandomOrder()->take(4)->get();

        return view('home', compact('topBooks', 'featuredBooks'));
    }
}
