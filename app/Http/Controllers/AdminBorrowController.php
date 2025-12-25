<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Borrowing;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdminBorrowController extends Controller
{
    public function index(Request $request)
    {
        $query = Borrowing::with(['student', 'book'])->orderBy('created_at', 'desc');

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        if ($request->has('type') && $request->type != '') {
            $query->where('type', $request->type);
        }

        $borrowings = $query->get();

        return view('admin.borrows.index', compact('borrowings'));
    }

    public function approve($id)
    {
        $borrow = Borrowing::findOrFail($id);
        $borrow->update(['status' => 'active']);

        return redirect()->back()->with('success', 'Peminjaman disetujui. Buku muncul di profil mahasiswa.');
    }

    public function reject($id)
    {
        $borrow = Borrowing::findOrFail($id);

        $book = Book::findOrFail($borrow->book_id);
        $book->increment('stock');

        $borrow->update(['status' => 'rejected']);

        return redirect()->back()->with('success', 'Permintaan ditolak. Stok buku dikembalikan.');
    }

    public function returnBook($id)
    {
        $borrow = Borrowing::findOrFail($id);

        if ($borrow->status == 'returned') {
            return back()->with('error', 'Buku ini sudah dikembalikan.');
        }

        $book = Book::findOrFail($borrow->book_id);
        $book->increment('stock');

        $borrow->update([
            'status' => 'returned',
            'updated_at' => Carbon::now()
        ]);

        return redirect()->back()->with('success', 'Pengembalian disetujui. Stok fisik telah ditambahkan.');
    }

    public function verificationIndex()
    {
        $returns = Borrowing::with(['student', 'book'])
            ->where('status', 'return_pending')
            ->orderBy('updated_at', 'asc')
            ->get();

        return view('admin.borrows.verification', compact('returns'));
    }

    public function verifyReturn($id)
    {
        $borrowing = Borrowing::findOrFail($id);

        $borrowing->update([
            'status' => 'returned',
            'return_date' => now(),
        ]);

        $book = $borrowing->book;

        if ($borrowing->type == 'offline') {
            $book->increment('stock');
        } else {
            $book->increment('stock_online');
        }

        return redirect()->back()->with('success', 'Buku berhasil diverifikasi dan stok telah diperbarui.');
    }

    public function create()
    {
        $students = Student::orderBy('name', 'asc')->get();

        $books = Book::where('stock', '>', 0)
            ->orderBy('title', 'asc')
            ->get();

        return view('admin.borrows.create', compact('students', 'books'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'book_id'    => 'required|exists:books,id',
            'duration'   => 'required|integer|min:1|max:30',
        ]);

        DB::beginTransaction();
        try {
            $book = Book::findOrFail($request->book_id);

            if ($book->stock < 1) {
                return back()->with('error', 'Stok buku habis!');
            }

            $book->decrement('stock');

            Borrowing::create([
                'student_id'    => $request->student_id,
                'book_id'       => $request->book_id,
                'type'          => 'offline',
                'borrow_date'   => Carbon::now(),
                'return_date'   => Carbon::now()->addDays((int) $request->duration),
                'status'        => 'active',
                'ticket_number' => 'OFF-' . strtoupper(Str::random(6)),
            ]);

            DB::commit();
            return redirect()->route('admin.borrows.index')->with('success', 'Peminjaman Offline berhasil dicatat!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
