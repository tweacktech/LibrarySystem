<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Payment;
use App\Enums\BorrowedStatus;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $borrowedBooks = collect();
        $payments = collect();
        $pendingPayments = collect();

        if ($user) {
            $borrowedBooks = Transaction::with(['book', 'user'])
                ->where('user_id', $user->id)
                ->whereIn('status', [BorrowedStatus::Borrowed, BorrowedStatus::Delayed])
                ->latest()
                ->get();

            $payments = Payment::with(['book'])
                ->where('user_id', $user->id)
                ->where('status', 'completed')
                ->latest()
                ->take(5)
                ->get();

            $pendingPayments = Payment::with(['book'])
                ->where('user_id', $user->id)
                ->where('status', 'pending')
                ->latest()
                ->get();
        }

        return view('dashboard', compact('borrowedBooks', 'payments', 'pendingPayments'));
    }
}
