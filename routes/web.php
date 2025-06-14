<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\BookBorrowController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\PaymentController as AdminPaymentController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/payment/verify', [PaymentController::class, 'verify'])->name('payment.verify');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/books/{bookBorrow}/lost', [BookBorrowController::class, 'showLostForm'])->name('books.lost.form');
    Route::post('/books/{bookBorrow}/return', [BookBorrowController::class, 'returnBook'])->name('books.return');
    Route::post('/books/{bookBorrow}/lost', [BookBorrowController::class, 'markAsLost'])->name('books.lost');
    Route::get('/payment/process/{reference}', [App\Http\Controllers\PaymentController::class, 'process'])->name('payment.process');
    Route::get('/payment/verify', [App\Http\Controllers\PaymentController::class, 'verify'])->name('payment.verify');
});

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/payments', [AdminPaymentController::class, 'index'])->name('payments.index');
});
