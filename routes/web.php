<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\BookBorrowController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\PaymentController as AdminPaymentController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/payment/verify', [PaymentController::class, 'verify'])->name('payment.verify');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/books/{bookBorrow}/lost', [BookBorrowController::class, 'showLostForm'])->name('books.lost.form');
    Route::post('/books/{bookBorrow}/return', [BookBorrowController::class, 'returnBook'])->name('books.return');
    Route::post('/books/{bookBorrow}/lost', [BookBorrowController::class, 'markAsLost'])->name('books.lost');

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Logout Route
    Route::post('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/');
    })->name('logout');
});

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/payments', [AdminPaymentController::class, 'index'])->name('payments.index');
});
