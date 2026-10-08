<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Category;
use App\Services\Circulation;
use App\Support\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('q'));

        $books = Book::with('category')
            ->withCount([
                'borrowings as on_loan' => fn ($q) => $q->where('type', 'offline')->whereIn('status', ['pending', 'active']),
                'reservations as waiting' => fn ($q) => $q->where('status', 'waiting'),
            ])
            ->when($search !== '', fn ($q) => $q->where(fn ($q) => $q
                ->whereLike('title', "%{$search}%")
                ->orWhereLike('author', "%{$search}%")
                ->orWhereLike('isbn', "%{$search}%")))
            ->when($request->query('category'), fn ($q, $id) => $q->where('category_id', $id))
            ->orderBy('title')
            ->paginate(20)
            ->withQueryString();

        return view('admin.books.index', [
            'books' => $books,
            'categories' => Category::orderBy('name')->get(),
            'search' => $search,
        ]);
    }

    public function create()
    {
        return view('admin.books.form', ['book' => new Book(['floor' => 1]), 'categories' => Category::orderBy('name')->get()]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        if ($request->hasFile('cover')) {
            $data['cover'] = Media::store($request->file('cover'), 'covers');
        }

        Book::create($data);

        return redirect()->route('admin.books.index')->with('success', 'Buku "'.$data['title'].'" ditambahkan.');
    }

    public function edit(Book $book)
    {
        return view('admin.books.form', ['book' => $book, 'categories' => Category::orderBy('name')->get()]);
    }

    public function update(Request $request, Book $book, Circulation $circulation)
    {
        $data = $this->validated($request, $book);

        if ($request->hasFile('cover')) {
            $data['cover'] = Media::store($request->file('cover'), 'covers');
            Media::delete($book->cover);
        }

        // Stok tidak ditulis langsung: tambahan eksemplar harus melayani antrean dulu.
        $book->update(Arr::except($data, 'stock'));
        $circulation->restock($book, $data['stock']);

        return redirect()->route('admin.books.index')->with('success', 'Data "'.$book->title.'" diperbarui.');
    }

    public function destroy(Book $book)
    {
        if ($book->borrowings()->exists()) {
            return back()->with('error', 'Buku ini punya riwayat peminjaman, jadi tidak dihapus supaya laporan tetap utuh. Set stoknya ke 0 kalau sudah tidak beredar.');
        }

        Media::delete($book->cover);
        $book->delete();

        return redirect()->route('admin.books.index')->with('success', 'Buku dihapus.');
    }

    public function storeCategory(Request $request)
    {
        $data = $request->validate(['name' => 'required|string|max:100|unique:categories,name']);
        Category::create($data);

        return back()->with('success', 'Kategori "'.$data['name'].'" ditambahkan.');
    }

    private function validated(Request $request, ?Book $book = null): array
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'isbn' => ['nullable', 'string', 'max:20', Rule::unique('books', 'isbn')->ignore($book?->id)],
            'author' => 'required|string|max:255',
            'publisher' => 'nullable|string|max:255',
            'year' => 'required|integer|min:1900|max:'.(now()->year + 1),
            'category_id' => 'required|exists:categories,id',
            'stock' => 'required|integer|min:0',
            'stock_online' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'digital_link' => 'nullable|url',
            'floor' => 'required|integer|min:1|max:10',
            'shelf_code' => 'required|string|max:50',
            'cover' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);
        unset($data['cover']);

        return $data;
    }
}
