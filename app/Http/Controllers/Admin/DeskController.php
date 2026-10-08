<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Borrowing;
use App\Models\Student;
use App\Services\Circulation;
use Illuminate\Http\Request;

/**
 * Meja layanan. Admin memindai QR tiket atau kartu anggota (atau mengetik kodenya),
 * lalu mendapat satu kartu berisi aksi yang relevan untuk kode itu.
 */
class DeskController extends Controller
{
    public function index()
    {
        return view('admin.desk.index', [
            'tickets' => Borrowing::with(['student', 'book'])->where('status', 'pending')->orderBy('pickup_expires_at')->take(8)->get(),
        ]);
    }

    public function lookup(Request $request)
    {
        $code = strtoupper(trim((string) $request->query('kode')));

        if ($code === '') {
            return redirect()->route('admin.desk');
        }

        $ticket = Borrowing::with(['student', 'book'])->where('ticket_number', $code)->first();
        if ($ticket) {
            return view('admin.desk.ticket', ['ticket' => $ticket, 'code' => $code]);
        }

        $student = Student::where('nim', $code)->first();
        if ($student) {
            return view('admin.desk.student', [
                'student' => $student,
                'code' => $code,
                'loans' => $student->borrowings()->with('book')->open()->orderBy('status')->get(),
                'unpaid' => $student->borrowings()->with('book')->unpaidFine()->get(),
                'books' => Book::where('stock', '>', 0)->orderBy('title')->get(['id', 'title', 'author', 'stock']),
            ]);
        }

        return redirect()->route('admin.desk')
            ->with('error', "Kode \"{$code}\" tidak cocok dengan tiket maupun NIM mana pun.")
            ->withInput(['kode' => $code]);
    }

    /** Peminjaman langsung di meja: buat tiket lalu serahkan dalam satu langkah. */
    public function lend(Request $request, Student $student, Circulation $circulation)
    {
        $request->validate(['book_id' => 'required|exists:books,id']);

        $ticket = $circulation->requestLoan($student, Book::findOrFail($request->book_id));
        $circulation->handOver($ticket);

        return redirect()->route('admin.desk.lookup', ['kode' => $student->nim])
            ->with('success', 'Buku diserahkan. Jatuh tempo '.$ticket->fresh()->due_at->translatedFormat('j F Y').'.');
    }
}
