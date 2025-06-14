<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookBorrow extends Model
{
    protected $fillable = [
        'user_id',
        'book_id',
        'borrow_date',
        'due_date',
        'return_date',
        'status',
        'is_lost',
        'lost_book_price',
    ];

    protected $casts = [
        'borrow_date' => 'datetime',
        'due_date' => 'datetime',
        'return_date' => 'datetime',
        'is_lost' => 'boolean',
    ];

    public function calculateLateReturnFee(): float
    {
        if (!$this->return_date || !$this->due_date) {
            return 0;
        }

        $daysLate = max(0, $this->return_date->diffInDays($this->due_date));
        $dailyRate = config('library.late_return_daily_rate', 100);

        return $daysLate * $dailyRate;
    }

    public function markAsLost(float $price): void
    {
        $this->update([
            'is_lost' => true,
            'lost_book_price' => $price,
            'status' => 'lost',
        ]);
    }
}
