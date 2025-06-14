<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BookBorrow;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $borrowedBooks = collect();
        if ($user) {
            $borrowedBooks = BookBorrow::with(['book', 'user'])
                ->where('user_id', $user->id)
                ->latest()
                ->get();
        }
        return view('dashboard', compact('borrowedBooks'));
    }
} 