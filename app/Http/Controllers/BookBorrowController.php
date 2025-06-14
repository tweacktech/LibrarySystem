<?php

namespace App\Http\Controllers;

use App\Models\BookBorrow;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class BookBorrowController extends Controller
{
    public function returnBook(Request $request, BookBorrow $bookBorrow)
    {
        $bookBorrow->update([
            'return_date' => now(),
            'status' => 'returned'
        ]);

        $lateFee = $bookBorrow->calculateLateReturnFee();

        if ($lateFee > 0) {
            return app(PaymentService::class)->initializeLateReturnPayment(
                $bookBorrow->user,
                $bookBorrow->book,
                $bookBorrow->return_date->diffInDays($bookBorrow->due_date)
            );
        }

        return redirect()->route('dashboard')->with('success', 'Book returned successfully');
    }

    public function markAsLost(Request $request, BookBorrow $bookBorrow)
    {
        $request->validate([
            'price' => 'required|numeric|min:0'
        ]);

        $bookBorrow->markAsLost($request->price);

        return app(PaymentService::class)->initializeLostBookPayment(
            $bookBorrow->user,
            $bookBorrow->book,
            $request->price
        );
    }

    public function showLostForm(BookBorrow $bookBorrow)
    {
        return view('books.lost', compact('bookBorrow'));
    }
}
