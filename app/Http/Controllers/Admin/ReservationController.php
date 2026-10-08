<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Reservation;

/**
 * Antrean berjalan sendiri: eksemplar yang kembali otomatis jadi tiket untuk orang terdepan.
 * Halaman ini hanya untuk melihat antrean per buku dan mengeluarkan seseorang bila perlu.
 */
class ReservationController extends Controller
{
    public function index()
    {
        $books = Book::whereHas('waitingReservations')
            ->with('waitingReservations.student')
            ->withCount([
                'borrowings as on_loan' => fn ($q) => $q->where('type', 'offline')->where('status', 'active'),
            ])
            ->get()
            ->sortByDesc(fn ($book) => $book->waitingReservations->count());

        return view('admin.reservations.index', ['books' => $books]);
    }

    public function cancel(Reservation $reservation)
    {
        if ($reservation->status !== 'waiting') {
            return back()->with('error', 'Antrean ini sudah tidak aktif.');
        }

        $reservation->update(['status' => 'cancelled']);

        return back()->with('success', $reservation->student->name.' dikeluarkan dari antrean.');
    }
}
