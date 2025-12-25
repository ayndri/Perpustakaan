<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminBookController;
use App\Http\Controllers\AdminBookRequestController;
use App\Http\Controllers\AdminBorrowController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminReportController;
use App\Http\Controllers\AdminReservationController;
use App\Http\Controllers\AdminStudentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\BorrowController;
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
Route::get('/books/{id}', [BookController::class, 'show'])->name('books.show');

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth:student')->group(function () {
    Route::post('/borrow/{book}', [BorrowController::class, 'store'])->name('borrow.store');
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::post('/borrow/{id}/return', [BorrowController::class, 'returnBook'])->name('borrow.return');

    Route::post('/books/{id}/review', [ReviewController::class, 'store'])->name('reviews.store');
    Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');
    Route::post('/favorites/{book}/toggle', [FavoriteController::class, 'toggle'])->name('favorites.toggle');

    Route::get('/verifikasi-akun', [VerificationController::class, 'index'])->name('verification.index');
    Route::post('/verifikasi-akun', [VerificationController::class, 'store'])->name('verification.store');

    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::post('/books/{id}/reserve', [ReservationController::class, 'store'])->name('reservations.store');
    Route::post('/reservations/{id}/cancel', [ReservationController::class, 'cancel'])->name('reservations.cancel');

    Route::get('/notification/{id}/read', [ProfileController::class, 'markAsRead'])->name('markAsRead');

    Route::get('/requests', [StudentBookRequestController::class, 'index'])->name('student.requests.index');
    Route::get('/requests/create', [StudentBookRequestController::class, 'create'])->name('student.requests.create');
    Route::post('/requests', [StudentBookRequestController::class, 'store'])->name('student.requests.store');
});

Route::prefix('admin')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
    Route::post('/login', [AdminAuthController::class, 'login']);
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
});

Route::prefix('admin')->middleware('auth')->group(function () {

    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    Route::resource('books', AdminBookController::class)->names([
        'index'   => 'admin.books.index',
        'create'  => 'admin.books.create',
        'store'   => 'admin.books.store',
        'show'    => 'admin.books.show',
        'edit'    => 'admin.books.edit',
        'update'  => 'admin.books.update',
        'destroy' => 'admin.books.destroy',
    ]);

    Route::get('/borrows', [AdminBorrowController::class, 'index'])->name('admin.borrows.index');
    Route::post('/borrows/{id}/approve', [AdminBorrowController::class, 'approve'])->name('admin.borrows.approve');
    Route::post('/borrows/{id}/reject', [AdminBorrowController::class, 'reject'])->name('admin.borrows.reject');
    Route::post('/borrows/{id}/return', [AdminBorrowController::class, 'returnBook'])->name('admin.borrows.return');

    Route::get('/borrows/create', [AdminBorrowController::class, 'create'])->name('admin.borrows.create');
    Route::post('/borrows', [AdminBorrowController::class, 'store'])->name('admin.borrows.store');

    Route::get('/verifikasi-pengembalian', [AdminBorrowController::class, 'verificationIndex'])->name('admin.borrows.verification');
    Route::post('/verifikasi-pengembalian/{id}', [AdminBorrowController::class, 'verifyReturn'])->name('admin.borrows.verify');

    Route::get('/verifikasi-mahasiswa', [AdminStudentController::class, 'index'])->name('admin.students.verification');
    Route::post('/verifikasi-mahasiswa/{id}/approve', [AdminStudentController::class, 'approve'])->name('admin.students.approve');
    Route::post('/verifikasi-mahasiswa/{id}/reject', [AdminStudentController::class, 'reject'])->name('admin.students.reject');

    Route::get('/students/create', [AdminStudentController::class, 'create'])->name('admin.students.create');
    Route::post('/students', [AdminStudentController::class, 'store'])->name('admin.students.store');

    Route::get('/all-students', [AdminStudentController::class, 'listanggota'])->name('admin.students.listanggota');
    Route::get('/students/{id}/history', [AdminStudentController::class, 'history'])->name('admin.students.history');

    Route::get('/reservations', [AdminReservationController::class, 'index'])->name('admin.reservations.index');
    Route::post('/reservations/{id}/available', [AdminReservationController::class, 'makeAvailable'])->name('admin.reservations.available');
    Route::post('/reservations/{id}/complete', [AdminReservationController::class, 'complete'])->name('admin.reservations.complete');
    Route::post('/reservations/{id}/cancel', [AdminReservationController::class, 'cancel'])->name('admin.reservations.cancel');

    Route::get('/reports', [AdminReportController::class, 'index'])->name('admin.reports.index');
    Route::get('/reports/borrowings', [AdminReportController::class, 'printBorrowings'])->name('admin.reports.print_borrowings');
    Route::get('/students/{id}/print-card', [AdminReportController::class, 'printMemberCard'])->name('admin.students.print_card');

    Route::get('/reports/members', [AdminReportController::class, 'printMembers'])->name('admin.reports.print_members');

    Route::get('/requests', [AdminBookRequestController::class, 'index'])->name('admin.requests.index');
    Route::patch('/requests/{id}', [AdminBookRequestController::class, 'updateStatus'])->name('admin.requests.update');
});
