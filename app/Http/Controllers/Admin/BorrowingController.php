<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use App\Services\Circulation;
use Illuminate\Http\Request;

class BorrowingController extends Controller
{
    public const FILTERS = [
        'pending' => 'Menunggu diambil',
        'active' => 'Sedang dipinjam',
        'overdue' => 'Lewat jatuh tempo',
        'fines' => 'Denda belum lunas',
        'history' => 'Riwayat',
    ];

    public function __construct(private Circulation $circulation) {}

    public function index(Request $request)
    {
        $filter = array_key_exists($request->query('filter'), self::FILTERS) ? $request->query('filter') : 'active';
        $search = trim((string) $request->query('q'));

        $borrowings = Borrowing::with(['student', 'book'])
            ->when($filter === 'pending', fn ($q) => $q->where('status', 'pending')->orderBy('pickup_expires_at'))
            ->when($filter === 'active', fn ($q) => $q->where('status', 'active')->orderBy('due_at'))
            ->when($filter === 'overdue', fn ($q) => $q->overdue()->orderBy('due_at'))
            ->when($filter === 'fines', fn ($q) => $q->unpaidFine()->latest('returned_at'))
            ->when($filter === 'history', fn ($q) => $q->whereIn('status', ['returned', 'expired', 'rejected', 'cancelled'])->latest('updated_at'))
            ->when($search !== '', fn ($q) => $q->where(fn ($q) => $q
                ->whereLike('ticket_number', "%{$search}%")
                ->orWhereHas('student', fn ($q) => $q->whereLike('name', "%{$search}%")->orWhereLike('nim', "%{$search}%"))
                ->orWhereHas('book', fn ($q) => $q->whereLike('title', "%{$search}%"))))
            ->paginate(20)
            ->withQueryString();

        return view('admin.borrowings.index', [
            'borrowings' => $borrowings,
            'filter' => $filter,
            'filters' => self::FILTERS,
            'search' => $search,
        ]);
    }

    public function handOver(Borrowing $borrowing)
    {
        $borrowing = $this->circulation->handOver($borrowing);

        return back()->with('success', "Diserahkan ke {$borrowing->student->name}. Jatuh tempo ".$borrowing->due_at->translatedFormat('j F Y').'.');
    }

    public function receive(Borrowing $borrowing)
    {
        $borrowing = $this->circulation->receiveReturn($borrowing);

        $message = 'Buku diterima kembali.';
        if ($borrowing->fine_amount > 0) {
            $message .= ' Terlambat '.$borrowing->daysLate().' hari, denda Rp'.number_format($borrowing->fine_amount, 0, ',', '.').'.';
        }

        return back()->with('success', $message);
    }

    public function reject(Borrowing $borrowing)
    {
        $this->circulation->reject($borrowing);

        return back()->with('success', 'Tiket ditolak dan eksemplarnya dilepas.');
    }

    public function payFine(Borrowing $borrowing)
    {
        $this->circulation->markFinePaid($borrowing);

        return back()->with('success', 'Denda Rp'.number_format($borrowing->fine_amount, 0, ',', '.').' tercatat lunas.');
    }
}
