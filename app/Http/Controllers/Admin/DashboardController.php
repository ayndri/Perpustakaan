<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BookRequest;
use App\Models\Borrowing;
use App\Models\Reservation;
use App\Models\Student;
use Illuminate\Support\Facades\DB;

/**
 * Halaman pertama admin menjawab satu pertanyaan: apa yang harus diurus hari ini?
 * Jadi yang diutamakan adalah daftar pekerjaan (tiket menunggu, buku telat, KTM),
 * bukan statistik.
 */
class DashboardController extends Controller
{
    public function index()
    {
        $tickets = Borrowing::with(['student', 'book'])
            ->where('status', 'pending')
            ->orderBy('pickup_expires_at')
            ->get();

        $overdue = Borrowing::with(['student', 'book'])->overdue()->orderBy('due_at')->get();

        $dueToday = Borrowing::with(['student', 'book'])
            ->where('status', 'active')
            ->where('type', 'offline')
            ->whereBetween('due_at', [now(), now()->endOfDay()])
            ->get();

        $loansPerMonth = Borrowing::select(
            DB::raw('CAST(EXTRACT(MONTH FROM handed_over_at) AS INTEGER) as month'),
            DB::raw('COUNT(*) as total')
        )
            ->whereNotNull('handed_over_at')
            ->whereYear('handed_over_at', now()->year)
            ->groupBy('month')
            ->pluck('total', 'month');

        return view('admin.dashboard', [
            'tickets' => $tickets,
            'overdue' => $overdue,
            'dueToday' => $dueToday,
            'pendingVerifications' => Student::where('verification_status', 'pending')->count(),
            'pendingRequests' => BookRequest::where('status', 'pending')->count(),
            'waitingReservations' => Reservation::where('status', 'waiting')->count(),
            'activeLoans' => Borrowing::where('status', 'active')->count(),
            'unpaidFines' => (int) Borrowing::unpaidFine()->sum('fine_amount'),
            'loansPerMonth' => collect(range(1, 12))->map(fn ($m) => (int) ($loansPerMonth[$m] ?? 0)),
        ]);
    }
}
