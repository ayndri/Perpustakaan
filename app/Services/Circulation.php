<?php

namespace App\Services;

use App\Exceptions\CirculationException;
use App\Models\Book;
use App\Models\Borrowing;
use App\Models\Reservation;
use App\Models\Student;
use App\Notifications\TicketReady;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Semua perubahan stok lewat kelas ini.
 *
 * Aturannya:
 *  - books.stock = eksemplar di rak yang bebas dipinjam. Eksemplar yang ditahan untuk
 *    tiket (status pending) sudah dikurangi dari stock.
 *  - Setiap operasi yang menyentuh stok mengunci baris buku (SELECT ... FOR UPDATE)
 *    di dalam transaksi, jadi dua permintaan untuk eksemplar terakhir dilayani bergantian.
 *  - Eksemplar yang kembali ke rak tidak langsung masuk stock kalau ada antrean:
 *    ia diberikan ke orang pertama di antrean sebagai tiket.
 *  - Tidak ada cron. Tiket kedaluwarsa dan e-book lewat masa pinjam dilepas oleh
 *    sweep() yang dipanggil di setiap request, sebelum halaman membaca stok.
 */
class Circulation
{
    public function requestLoan(Student $student, Book $book): Borrowing
    {
        return DB::transaction(function () use ($student, $book) {
            $student = $this->lockStudent($student);
            $book = $this->lockBook($book);
            $this->releaseExpired($book);

            $this->ensureCanBorrow($student, $book);

            if ($book->stock < 1) {
                throw new CirculationException('Eksemplar fisik sedang habis. Kamu bisa masuk antrean, nanti dikabari saat giliranmu.');
            }

            $book->decrement('stock');

            return $this->issueTicket($student, $book, config('library.pickup_hours'));
        });
    }

    public function borrowEbook(Student $student, Book $book): Borrowing
    {
        return DB::transaction(function () use ($student, $book) {
            $student = $this->lockStudent($student);
            $book = $this->lockBook($book);
            $this->releaseExpired($book);

            $this->ensureCanBorrow($student, $book);

            if (! $book->digital_link) {
                throw new CirculationException('Buku ini tidak punya versi e-book.');
            }
            if ($book->stock_online < 1) {
                throw new CirculationException('Kuota baca e-book sedang penuh. Coba lagi setelah ada yang selesai membaca.');
            }

            $book->decrement('stock_online');

            return Borrowing::create([
                'student_id' => $student->id,
                'book_id' => $book->id,
                'type' => 'online',
                'status' => 'active',
                'ticket_number' => $this->newTicketNumber('EB'),
                'handed_over_at' => now(),
                'due_at' => now()->addDays(config('library.ebook_days')),
            ]);
        });
    }

    /** Admin menyerahkan buku fisik di meja. */
    public function handOver(Borrowing $borrowing): Borrowing
    {
        return DB::transaction(function () use ($borrowing) {
            $book = $this->lockBook($borrowing->book);
            $this->releaseExpired($book);
            $borrowing = Borrowing::lockForUpdate()->findOrFail($borrowing->id);

            if ($borrowing->status === 'expired') {
                throw new CirculationException('Tiket ini sudah kedaluwarsa, eksemplarnya sudah dilepas. Minta mahasiswa membuat tiket baru.');
            }
            if ($borrowing->status !== 'pending') {
                throw new CirculationException('Tiket ini tidak sedang menunggu diambil.');
            }

            $borrowing->update([
                'status' => 'active',
                'handed_over_at' => now(),
                'due_at' => now()->addDays(config('library.loan_days'))->endOfDay(),
            ]);

            return $borrowing;
        });
    }

    /** Admin menerima buku fisik kembali. Denda dikunci di sini. */
    public function receiveReturn(Borrowing $borrowing): Borrowing
    {
        return DB::transaction(function () use ($borrowing) {
            $book = $this->lockBook($borrowing->book);
            $borrowing = Borrowing::lockForUpdate()->findOrFail($borrowing->id);

            if ($borrowing->status !== 'active' || ! $borrowing->isOffline()) {
                throw new CirculationException('Buku ini tidak tercatat sedang dipinjam.');
            }

            $now = now();
            $borrowing->update([
                'status' => 'returned',
                'returned_at' => $now,
                'fine_amount' => $borrowing->daysLate($now) * config('library.fine_per_day'),
            ]);

            $this->releaseCopy($book);

            return $borrowing;
        });
    }

    public function returnEbook(Student $student, Borrowing $borrowing): void
    {
        DB::transaction(function () use ($student, $borrowing) {
            $book = $this->lockBook($borrowing->book);
            $borrowing = Borrowing::lockForUpdate()->findOrFail($borrowing->id);

            if ($borrowing->student_id !== $student->id || $borrowing->type !== 'online' || $borrowing->status !== 'active') {
                throw new CirculationException('E-book ini tidak sedang kamu pinjam.');
            }

            $borrowing->update(['status' => 'returned', 'returned_at' => now()]);
            $book->increment('stock_online');
        });
    }

    public function renew(Student $student, Borrowing $borrowing): Borrowing
    {
        return DB::transaction(function () use ($student, $borrowing) {
            $this->lockBook($borrowing->book);
            $borrowing = Borrowing::lockForUpdate()->findOrFail($borrowing->id);

            if ($borrowing->student_id !== $student->id || $borrowing->status !== 'active' || ! $borrowing->isOffline()) {
                throw new CirculationException('Peminjaman ini tidak bisa diperpanjang.');
            }
            if ($borrowing->isOverdue()) {
                throw new CirculationException('Peminjaman yang sudah lewat jatuh tempo tidak bisa diperpanjang. Kembalikan bukunya ke meja layanan.');
            }
            if ($borrowing->renewals >= config('library.max_renewals')) {
                throw new CirculationException('Kamu sudah memakai jatah perpanjangan untuk buku ini.');
            }
            if ($this->queueLength($borrowing->book_id) > 0) {
                throw new CirculationException('Ada mahasiswa lain yang sedang antre buku ini, jadi tidak bisa diperpanjang.');
            }

            $borrowing->update([
                'due_at' => $borrowing->due_at->addDays(config('library.renew_days')),
                'renewals' => $borrowing->renewals + 1,
            ]);

            return $borrowing;
        });
    }

    public function cancelTicket(Student $student, Borrowing $borrowing): void
    {
        $this->closePendingTicket($borrowing, 'cancelled', $student);
    }

    public function reject(Borrowing $borrowing): void
    {
        $this->closePendingTicket($borrowing, 'rejected');
    }

    /**
     * Admin mengubah jumlah eksemplar di rak (beli baru, rusak, hilang). Eksemplar tambahan
     * lewat releaseCopy, jadi antrean yang menunggu dilayani dulu sebelum masuk rak.
     */
    public function restock(Book $book, int $shelfCount): void
    {
        DB::transaction(function () use ($book, $shelfCount) {
            $book = $this->lockBook($book);
            $delta = $shelfCount - $book->stock;

            if ($delta < 0) {
                $book->decrement('stock', -$delta);
            }

            for ($i = 0; $i < $delta; $i++) {
                $this->releaseCopy($book);
            }
        });
    }

    public function markFinePaid(Borrowing $borrowing): void
    {
        if (! $borrowing->hasUnpaidFine()) {
            throw new CirculationException('Tidak ada denda yang perlu dilunasi di peminjaman ini.');
        }

        $borrowing->update(['fine_paid_at' => now()]);
    }

    public function reserve(Student $student, Book $book): Reservation
    {
        return DB::transaction(function () use ($student, $book) {
            $student = $this->lockStudent($student);
            $book = $this->lockBook($book);
            $this->releaseExpired($book);

            $this->ensureVerified($student);

            if ($book->stock > 0) {
                throw new CirculationException('Eksemplar masih tersedia, langsung pinjam saja.');
            }
            if ($student->borrowings()->open()->where('book_id', $book->id)->exists()) {
                throw new CirculationException('Kamu sedang memegang tiket atau meminjam buku ini.');
            }
            if ($student->reservations()->where('book_id', $book->id)->where('status', 'waiting')->exists()) {
                throw new CirculationException('Kamu sudah ada di antrean buku ini.');
            }
            if ($student->reservations()->where('status', 'waiting')->count() >= config('library.max_active_reservations')) {
                throw new CirculationException('Kamu hanya boleh antre maksimal '.config('library.max_active_reservations').' buku sekaligus.');
            }

            return Reservation::create([
                'student_id' => $student->id,
                'book_id' => $book->id,
                'status' => 'waiting',
            ]);
        });
    }

    public function cancelReservation(Student $student, Reservation $reservation): void
    {
        if ($reservation->student_id !== $student->id || $reservation->status !== 'waiting') {
            throw new CirculationException('Antrean ini tidak bisa dibatalkan.');
        }

        $reservation->update(['status' => 'cancelled']);
    }

    /**
     * Lepas tiket yang tidak diambil dan e-book yang masa pinjamnya habis.
     * Satu query murah kalau tidak ada yang kedaluwarsa, jadi aman dipanggil tiap request.
     */
    public function sweep(): void
    {
        $bookIds = Borrowing::query()
            ->where(fn ($q) => $q
                ->where(fn ($q) => $q->where('status', 'pending')->where('pickup_expires_at', '<', now()))
                ->orWhere(fn ($q) => $q->where('status', 'active')->where('type', 'online')->where('due_at', '<', now())))
            ->distinct()
            ->pluck('book_id');

        foreach ($bookIds as $bookId) {
            DB::transaction(function () use ($bookId) {
                $book = Book::lockForUpdate()->find($bookId);
                if ($book) {
                    $this->releaseExpired($book);
                }
            });
        }
    }

    public function queueLength(int $bookId): int
    {
        return Reservation::where('book_id', $bookId)->where('status', 'waiting')->count();
    }

    // ---------------------------------------------------------------------------------

    /** Harus dipanggil dengan baris buku sudah dikunci. */
    private function releaseExpired(Book $book): void
    {
        $expiredTickets = Borrowing::where('book_id', $book->id)
            ->where('status', 'pending')
            ->where('pickup_expires_at', '<', now())
            ->lockForUpdate()
            ->get();

        foreach ($expiredTickets as $ticket) {
            $ticket->update(['status' => 'expired']);
            $this->releaseCopy($book);
        }

        $endedEbooks = Borrowing::where('book_id', $book->id)
            ->where('status', 'active')
            ->where('type', 'online')
            ->where('due_at', '<', now())
            ->lockForUpdate()
            ->get();

        foreach ($endedEbooks as $loan) {
            $loan->update(['status' => 'returned', 'returned_at' => $loan->due_at]);
            $book->increment('stock_online');
        }
    }

    /** Satu eksemplar fisik kembali tersedia: berikan ke antrean terdepan, atau taruh di rak. */
    private function releaseCopy(Book $book): void
    {
        $next = Reservation::where('book_id', $book->id)
            ->where('status', 'waiting')
            ->orderBy('created_at')
            ->orderBy('id')
            ->lockForUpdate()
            ->first();

        if (! $next) {
            $book->increment('stock');

            return;
        }

        $ticket = $this->issueTicket($next->student, $book, config('library.reservation_pickup_hours'));
        $next->update(['status' => 'fulfilled', 'borrowing_id' => $ticket->id]);

        DB::afterCommit(fn () => $next->student->notify(new TicketReady($ticket)));
    }

    private function issueTicket(Student $student, Book $book, int $pickupHours): Borrowing
    {
        return Borrowing::create([
            'student_id' => $student->id,
            'book_id' => $book->id,
            'type' => 'offline',
            'status' => 'pending',
            'ticket_number' => $this->newTicketNumber('TK'),
            'pickup_expires_at' => now()->addHours($pickupHours),
        ]);
    }

    private function closePendingTicket(Borrowing $borrowing, string $status, ?Student $owner = null): void
    {
        DB::transaction(function () use ($borrowing, $status, $owner) {
            $book = $this->lockBook($borrowing->book);
            $borrowing = Borrowing::lockForUpdate()->findOrFail($borrowing->id);

            if ($owner && $borrowing->student_id !== $owner->id) {
                throw new CirculationException('Tiket ini bukan milikmu.');
            }
            if ($borrowing->status !== 'pending') {
                throw new CirculationException('Tiket ini sudah tidak menunggu diambil.');
            }

            $borrowing->update(['status' => $status]);
            $this->releaseCopy($book);
        });
    }

    private function ensureVerified(Student $student): void
    {
        if ($student->verification_status !== 'verified') {
            throw new CirculationException('Verifikasi KTM dulu sebelum meminjam atau antre buku.');
        }
    }

    private function ensureCanBorrow(Student $student, Book $book): void
    {
        $this->ensureVerified($student);

        if ($student->borrowings()->overdue()->exists()) {
            throw new CirculationException('Ada buku yang lewat jatuh tempo. Kembalikan dulu sebelum meminjam lagi.');
        }

        $unpaid = (int) $student->borrowings()->unpaidFine()->sum('fine_amount');
        if ($unpaid > 0) {
            throw new CirculationException('Masih ada denda Rp'.number_format($unpaid, 0, ',', '.').' yang belum dilunasi. Lunasi di meja layanan dulu.');
        }

        if ($student->borrowings()->open()->where('book_id', $book->id)->exists()) {
            throw new CirculationException('Kamu sedang memegang tiket atau meminjam buku ini.');
        }

        $today = $student->borrowings()->whereBetween('created_at', [now()->startOfDay(), now()->endOfDay()])->count();
        if ($today >= config('library.daily_request_limit')) {
            throw new CirculationException('Batas harian tercapai: maksimal '.config('library.daily_request_limit').' peminjaman per hari.');
        }
    }

    private function lockBook(Book $book): Book
    {
        return Book::lockForUpdate()->findOrFail($book->id);
    }

    private function lockStudent(Student $student): Student
    {
        return Student::lockForUpdate()->findOrFail($student->id);
    }

    private function newTicketNumber(string $prefix): string
    {
        do {
            $number = $prefix.'-'.strtoupper(Str::random(6));
        } while (Borrowing::where('ticket_number', $number)->exists());

        return $number;
    }
}
