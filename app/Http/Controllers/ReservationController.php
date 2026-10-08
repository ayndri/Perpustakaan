<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Reservation;
use App\Services\Circulation;

class ReservationController extends Controller
{
    public function __construct(private Circulation $circulation) {}

    public function store(Book $book)
    {
        $reservation = $this->circulation->reserve(auth('student')->user(), $book);

        return back()->with('success', 'Kamu masuk antrean di urutan ke-'.$reservation->position().'. Kami kabari begitu eksemplarnya disisihkan untukmu.');
    }

    public function cancel(Reservation $reservation)
    {
        $this->circulation->cancelReservation(auth('student')->user(), $reservation);

        return back()->with('success', 'Kamu keluar dari antrean.');
    }
}
