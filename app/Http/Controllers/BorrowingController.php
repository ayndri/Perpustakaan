<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Borrowing;
use App\Services\Circulation;

class BorrowingController extends Controller
{
    public function __construct(private Circulation $circulation) {}

    public function store(Book $book)
    {
        $ticket = $this->circulation->requestLoan(auth('student')->user(), $book);

        return redirect()->route('borrowings.show', $ticket)
            ->with('success', 'Tiket dibuat. Tunjukkan QR ini di meja layanan sebelum batas ambil.');
    }

    public function ebook(Book $book)
    {
        $loan = $this->circulation->borrowEbook(auth('student')->user(), $book);

        return redirect()->route('books.show', $book)
            ->with('success', 'E-book dipinjam. Akses terbuka sampai '.$loan->due_at->translatedFormat('j F Y, H.i').', lalu kuotanya dikembalikan otomatis.');
    }

    public function show(Borrowing $borrowing)
    {
        $this->authorizeOwner($borrowing);
        $borrowing->load('book.category');

        return view('borrowings.show', ['borrowing' => $borrowing]);
    }

    /** Tautan e-book hanya dibuka lewat sini, supaya tidak bisa dipakai tanpa meminjam. */
    public function read(Borrowing $borrowing)
    {
        $this->authorizeOwner($borrowing);

        if ($borrowing->type !== 'online' || $borrowing->status !== 'active') {
            return redirect()->route('profile')->with('error', 'Akses e-book ini sudah berakhir.');
        }

        return redirect()->away($borrowing->book->digital_link);
    }

    public function cancel(Borrowing $borrowing)
    {
        $this->circulation->cancelTicket(auth('student')->user(), $borrowing);

        return redirect()->route('profile')->with('success', 'Tiket dibatalkan. Eksemplarnya dilepas untuk peminjam lain.');
    }

    public function renew(Borrowing $borrowing)
    {
        $borrowing = $this->circulation->renew(auth('student')->user(), $borrowing);

        return back()->with('success', 'Diperpanjang. Jatuh tempo baru: '.$borrowing->due_at->translatedFormat('j F Y').'.');
    }

    public function returnEbook(Borrowing $borrowing)
    {
        $this->circulation->returnEbook(auth('student')->user(), $borrowing);

        return back()->with('success', 'E-book dikembalikan. Terima kasih sudah memberi giliran ke yang lain.');
    }

    private function authorizeOwner(Borrowing $borrowing): void
    {
        abort_unless($borrowing->student_id === auth('student')->id(), 404);
    }
}
