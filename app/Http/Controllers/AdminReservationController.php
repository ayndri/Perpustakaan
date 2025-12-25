<?php

namespace App\Http\Controllers;

use App\Models\Borrowing;
use App\Models\Reservation;
use App\Notifications\ReservationAvailable;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class AdminReservationController extends Controller
{
    public function index()
    {
        $reservations = Reservation::with(['student', 'book'])
            ->whereIn('status', ['pending', 'available'])
            ->orderBy('created_at', 'asc')
            ->get();

        return view('admin.reservations.index', compact('reservations'));
    }

    public function makeAvailable($id)
    {
        $reservation = Reservation::findOrFail($id);

        if ($reservation->status != 'pending') {
            return back()->with('error', 'Status reservasi tidak valid.');
        }

        $reservation->update(['status' => 'available']);

        $reservation->student->notify(new ReservationAvailable($reservation));

        return back()->with('success', 'Status Available. Notifikasi email sedang dikirim ke mahasiswa.');
    }

    public function complete($id)
    {
        $reservation = Reservation::findOrFail($id);

        if ($reservation->book->stock <= 0) {
            return back()->with('error', 'Gagal memproses: Stok buku fisik di database 0. Pastikan stok tersedia sebelum menyelesaikan reservasi.');
        }

        DB::transaction(function () use ($reservation) {

            Borrowing::create([
                'student_id'    => $reservation->student_id,
                'book_id'       => $reservation->book_id,
                'borrow_date'   => Carbon::now(),
                'return_date'   => null,
                'status'        => 'active',
                'type'          => 'offline',
                'ticket_number' => 'RES-' . strtoupper(Str::random(8)),
            ]);

            $reservation->book->decrement('stock');

            $reservation->update([
                'status' => 'completed',
                'updated_at' => Carbon::now()
            ]);
        });

        return back()->with('success', 'Reservasi selesai! Data telah dipindahkan ke Peminjaman Aktif dan stok dikurangi.');
    }

    public function cancel($id)
    {
        $reservation = Reservation::findOrFail($id);
        $reservation->update(['status' => 'cancelled']);

        return back()->with('success', 'Reservasi telah dibatalkan/ditolak.');
    }
}
