<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\BorrowingController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\StudentBookRequestController;
use App\Http\Controllers\VerificationController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/books', [BookController::class, 'index'])->name('books.index');
Route::get('/books/{book}', [BookController::class, 'show'])->name('books.show');

Route::middleware('guest:student')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');
});
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth:student')->group(function () {
    Route::post('/books/{book}/borrow', [BorrowingController::class, 'store'])->name('borrow.store');
    Route::post('/books/{book}/ebook', [BorrowingController::class, 'ebook'])->name('borrow.ebook');
    Route::post('/books/{book}/reserve', [ReservationController::class, 'store'])->name('reservations.store');
    Route::post('/books/{book}/review', [ReviewController::class, 'store'])->name('reviews.store');
    Route::post('/books/{book}/favorite', [FavoriteController::class, 'toggle'])->name('favorites.toggle');

    Route::get('/tiket/{borrowing}', [BorrowingController::class, 'show'])->name('borrowings.show');
    Route::get('/baca/{borrowing}', [BorrowingController::class, 'read'])->name('borrowings.read');
    Route::post('/borrowings/{borrowing}/cancel', [BorrowingController::class, 'cancel'])->name('borrowings.cancel');
    Route::post('/borrowings/{borrowing}/renew', [BorrowingController::class, 'renew'])->name('borrowings.renew');
    Route::post('/borrowings/{borrowing}/return-ebook', [BorrowingController::class, 'returnEbook'])->name('borrowings.return-ebook');
    Route::post('/reservations/{reservation}/cancel', [ReservationController::class, 'cancel'])->name('reservations.cancel');

    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/notifications/{id}/read', [ProfileController::class, 'markAsRead'])->name('notifications.read');

    Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');

    Route::get('/verifikasi-akun', [VerificationController::class, 'index'])->name('verification.index');
    Route::post('/verifikasi-akun', [VerificationController::class, 'store'])->name('verification.store');

    Route::get('/requests', [StudentBookRequestController::class, 'index'])->name('student.requests.index');
    Route::get('/requests/create', [StudentBookRequestController::class, 'create'])->name('student.requests.create');
    Route::post('/requests', [StudentBookRequestController::class, 'store'])->name('student.requests.store');
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest:web')->group(function () {
        Route::get('/login', [Admin\AuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [Admin\AuthController::class, 'login'])->middleware('throttle:10,1');
    });
    Route::post('/logout', [Admin\AuthController::class, 'logout'])->name('logout');

    Route::middleware('auth:web')->group(function () {
        Route::get('/', fn () => redirect()->route('admin.dashboard'));
        Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');

        // Meja layanan: scan tiket/kartu anggota, lalu serahkan, terima kembali, atau lunasi denda.
        Route::get('/meja', [Admin\DeskController::class, 'index'])->name('desk');
        Route::get('/meja/cari', [Admin\DeskController::class, 'lookup'])->name('desk.lookup');
        Route::post('/meja/pinjamkan/{student}', [Admin\DeskController::class, 'lend'])->name('desk.lend');

        Route::get('/borrowings', [Admin\BorrowingController::class, 'index'])->name('borrowings.index');
        Route::post('/borrowings/{borrowing}/hand-over', [Admin\BorrowingController::class, 'handOver'])->name('borrowings.hand-over');
        Route::post('/borrowings/{borrowing}/receive', [Admin\BorrowingController::class, 'receive'])->name('borrowings.receive');
        Route::post('/borrowings/{borrowing}/reject', [Admin\BorrowingController::class, 'reject'])->name('borrowings.reject');
        Route::post('/borrowings/{borrowing}/pay-fine', [Admin\BorrowingController::class, 'payFine'])->name('borrowings.pay-fine');

        Route::resource('books', Admin\BookController::class)->except('show');
        Route::post('/categories', [Admin\BookController::class, 'storeCategory'])->name('categories.store');

        Route::get('/reservations', [Admin\ReservationController::class, 'index'])->name('reservations.index');
        Route::post('/reservations/{reservation}/cancel', [Admin\ReservationController::class, 'cancel'])->name('reservations.cancel');

        Route::get('/students', [Admin\StudentController::class, 'index'])->name('students.index');
        Route::get('/students/create', [Admin\StudentController::class, 'create'])->name('students.create');
        Route::post('/students', [Admin\StudentController::class, 'store'])->name('students.store');
        Route::get('/students/verification', [Admin\StudentController::class, 'verification'])->name('students.verification');
        Route::get('/students/{student}', [Admin\StudentController::class, 'show'])->name('students.show');
        Route::get('/students/{student}/ktm', [Admin\StudentController::class, 'ktm'])->name('students.ktm');
        Route::post('/students/{student}/approve', [Admin\StudentController::class, 'approve'])->name('students.approve');
        Route::post('/students/{student}/reject', [Admin\StudentController::class, 'reject'])->name('students.reject');

        Route::get('/requests', [Admin\BookRequestController::class, 'index'])->name('requests.index');
        Route::patch('/requests/{bookRequest}', [Admin\BookRequestController::class, 'update'])->name('requests.update');

        Route::get('/reports', [Admin\ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/borrowings', [Admin\ReportController::class, 'borrowings'])->name('reports.borrowings');
        Route::get('/reports/members', [Admin\ReportController::class, 'members'])->name('reports.members');
        Route::get('/students/{student}/card', [Admin\ReportController::class, 'memberCard'])->name('students.card');
    });
});
