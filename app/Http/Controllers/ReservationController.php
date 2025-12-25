<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Borrowing;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReservationController extends Controller
{
    public function store($bookId)
    {
        $studentId = Auth::guard('student')->id();

        $activeCount = Reservation::where('student_id', $studentId)
            ->whereIn('status', ['pending', 'available'])
            ->count();

        if ($activeCount >= 2) {
            return back()->with('error', 'Anda hanya boleh melakukan reservasi maksimal 2 buku sekaligus.');
        }

        $isBorrowing = Borrowing::where('student_id', $studentId)
            ->where('book_id', $bookId)
            ->where('status', 'active')
            ->exists();

        if ($isBorrowing) {
            return back()->with('error', 'Anda sedang meminjam buku ini. Tidak bisa booking.');
        }

        $book = Book::findOrFail($bookId);

        if ($book->stock > 0) {
            return back()->with('error', 'Stok buku tersedia, silakan langsung pinjam!');
        }

        $existing = Reservation::where('student_id', $studentId)
            ->where('book_id', $bookId)
            ->whereIn('status', ['pending', 'available'])
            ->first();

        if ($existing) {
            return back()->with('error', 'Anda sudah masuk antrian untuk buku ini.');
        }

        Reservation::create([
            'student_id' => $studentId,
            'book_id' => $bookId,
            'status' => 'pending'
        ]);

        return back()->with('success', 'Berhasil booking! Anda akan diberitahu jika buku sudah tersedia.');
    }

    public function cancel($id)
    {
        $reservation = Reservation::where('student_id', Auth::guard('student')->id())
            ->where('id', $id)
            ->firstOrFail();

        $reservation->update(['status' => 'cancelled']);

        return back()->with('success', 'Booking berhasil dibatalkan.');
    }
}
