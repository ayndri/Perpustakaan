<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Borrowing;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class BorrowController extends Controller
{
    public function store(Request $request, $bookId)
    {
        $student = Auth::guard('student')->user();

        if ($student->verification_status !== 'verified') {
            return redirect()->route('verification.index')
                ->with('error', 'Anda harus verifikasi KTM terlebih dahulu sebelum meminjam buku!');
        }

        $studentId = Auth::guard('student')->id();
        $book = Book::findOrFail($bookId);
        $type = $request->type;

        $borrowCountToday = Borrowing::where('student_id', $studentId)
            ->whereDate('created_at', Carbon::today())
            ->count();

        if ($borrowCountToday >= 3) {
            return back()->with('error', 'Batas harian tercapai! Kamu hanya boleh meminjam maksimal 3 buku dalam sehari.');
        }

        $isSedangPinjam = Borrowing::where('student_id', $studentId)
            ->where('book_id', $bookId)
            ->whereIn('status', ['active', 'pending', 'return_pending'])
            ->exists();

        if ($isSedangPinjam) {
            return back()->with('error', 'Kamu sedang meminjam buku ini. Kembalikan dulu sebelum meminjam lagi.');
        }

        if ($type == 'offline') {
            if ($book->stock > 0) {
                $book->decrement('stock');

                $ticket = 'TIK-' . Auth::guard('student')->user()->nim . '-' . date('dm') . '-' . strtoupper(Str::random(3));

                Borrowing::create([
                    'student_id' => $studentId,
                    'book_id' => $book->id,
                    'type' => 'offline',
                    'borrow_date' => Carbon::now(),
                    'return_date' => Carbon::now()->addDays(7),
                    'ticket_number' => $ticket,
                    'status' => 'pending'
                ]);

                return redirect()->route('profile')->with('success', 'Berhasil! Tiket peminjaman telah dibuat. Tunjukkan tiket di profil ini ke Admin.');
            } else {
                return back()->with('error', 'Stok buku fisik habis.');
            }
        } else if ($type == 'online') {
            if ($book->digital_link && $book->stock_online > 0) {
                $book->decrement('stock_online');

                Borrowing::create([
                    'student_id' => $studentId,
                    'book_id' => $book->id,
                    'type' => 'online',
                    'borrow_date' => Carbon::now(),
                    'return_date' => Carbon::now()->addDays(3),
                    'status' => 'active',
                ]);

                return redirect()->route('profile')->with('success', 'Berhasil meminjam E-Book! Akses bacaan telah ditambahkan ke profilmu.');
            } else {
                return back()->with('error', 'Kuota baca online penuh.');
            }
        }

        return back()->with('error', 'Gagal memproses peminjaman.');
    }

    public function returnBook($id)
    {
        $studentId = Auth::guard('student')->id();

        $borrow = Borrowing::where('student_id', $studentId)
            ->where('id', $id)
            ->firstOrFail();

        if ($borrow->status == 'returned') {
            return redirect()->back()->with('error', 'Buku ini sudah dikembalikan sebelumnya.');
        }

        if ($borrow->type == 'online') {
            $book = $borrow->book;
            $book->increment('stock_online');

            $borrow->update([
                'status'      => 'returned',
                'return_date' => Carbon::now(),
            ]);

            return redirect()->back()->with('success', 'E-Book berhasil dikembalikan. Kuota baca Anda telah dipulihkan.');
        } else {
            $borrow->update([
                'status' => 'return_pending'
            ]);

            return redirect()->back()->with('success', 'Permintaan pengembalian diajukan. Silakan serahkan buku fisik ke Admin untuk verifikasi.');
        }
    }
}
