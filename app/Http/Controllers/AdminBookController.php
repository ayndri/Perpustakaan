<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminBookController extends Controller
{
    public function index()
    {
        $books = Book::with('category')->latest()->get();

        return view('admin.books.index', compact('books'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.books.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'author' => 'required',
            'year' => 'required|numeric',
            'category_id' => 'required',
            'stock' => 'required|numeric|min:0',
            'stock_online' => 'required|numeric|min:0',
            'description' => 'nullable',
            'digital_link' => 'nullable|url',
            'floor' => 'required|numeric|min:1',
            'shelf_code' => 'required|string|max:50',
            'cover' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->all();

        if ($request->hasFile('cover')) {
            $path = $request->file('cover')->store('covers', 'public');
            $data['cover'] = $path;
        }

        Book::create($data);

        return redirect()->route('admin.books.index')
            ->with('success', 'Buku berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $book = Book::findOrFail($id);
        $categories = Category::all();
        return view('admin.books.edit', compact('book', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required',
            'author' => 'required',
            'year' => 'required|numeric',
            'category_id' => 'required',
            'stock' => 'required|numeric|min:0',
            'stock_online' => 'required|numeric|min:0',
            'description' => 'nullable',
            'digital_link' => 'nullable|url',
            'floor' => 'required|numeric|min:1',
            'shelf_code' => 'required|string|max:50',
            'cover' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $book = Book::findOrFail($id);
        $data = $request->all();

        if ($request->hasFile('cover')) {
            if ($book->cover && Storage::disk('public')->exists($book->cover)) {
                Storage::disk('public')->delete($book->cover);
            }

            $path = $request->file('cover')->store('covers', 'public');
            $data['cover'] = $path;
        }

        $book->update($data);

        return redirect()->route('admin.books.index')
            ->with('success', 'Data buku berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $book = Book::findOrFail($id);

        if ($book->cover && Storage::disk('public')->exists($book->cover)) {
            Storage::disk('public')->delete($book->cover);
        }

        $book->delete();

        return redirect()->route('admin.books.index')
            ->with('success', 'Buku berhasil dihapus!');
    }
}
