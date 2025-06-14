<?php

namespace App\Models;

use App\Enums\BorrowedStatus;
use App\Observers\TransactionObserver;
use App\Services\PaymentService;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

#[ObservedBy(TransactionObserver::class)]
class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'book_id',
        'user_id',
        'borrowed_date',
        'borrowed_for',
        'returned_date',
        'status',
        'fine',
    ];

    protected $casts = [
        'status' => BorrowedStatus::class,
        'borrowed_date' => 'date',
        'returned_date' => 'date',
        'fine' => 'integer',
        'borrowed_for' => 'integer',
    ];

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function processLateReturn(): mixed
    {
        if (!$this->returned_date || !$this->due_date) {
            return null;
        }

        $daysLate = max(0, $this->returned_date->diffInDays($this->due_date));

        if ($daysLate > 0) {
            $paymentService = app(PaymentService::class);
            return $paymentService->initializeLateReturnPayment($this->user, $this->book, $daysLate);
        }

        return null;
    }

    public function getDueDateAttribute(): Carbon
    {
        return Carbon::parse($this->borrowed_date)->addDays($this->borrowed_for);
    }

    public static function booted(): void
    {
        parent::boot();

        static::saving(function ($transaction) {
            $borrowedDate = Carbon::parse($transaction->borrowed_date);
            $borrowedFor = (int) $transaction->borrowed_for;
            $dueDate = $borrowedDate->addDays($borrowedFor);
            $delay = 0;
            $fine = 0;

            // Calculate delay and fine if there's a returned date
            if ($transaction->returned_date) {
                $returnDate = Carbon::parse($transaction->returned_date);
                if ($returnDate->gt($dueDate)) {
                    $delay = $dueDate->diffInDays($returnDate);
                    $fine = $delay * Config::get('library.fine_per_day');
                }
            } else {
                // If no returned date, check if the book is overdue
                $now = Carbon::now();
                if ($now->gt($dueDate)) {
                    $delay = $dueDate->diffInDays($now);
                    $fine = $delay * Config::get('library.fine_per_day');
                }
            }

            // Create a pending payment if the status is being changed to Delayed
            if ($transaction->isDirty('status') && $transaction->status === BorrowedStatus::Delayed && $fine > 0) {
                $paymentService = app(PaymentService::class);
                $paymentService->initializeLateReturnPayment($transaction->user, $transaction->book, $delay);
            }

            $transaction->fine = $fine;
        });

        static::created(function ($model) {
            $cacheKey = 'NavigationCount_'.class_basename($model).$model->getTable();
            if(Cache::has($cacheKey)) {
                Cache::forget($cacheKey);
            }
        });

        static::deleted(function ($model) {
            $cacheKey = 'NavigationCount_'.class_basename($model).$model->getTable();
            if(Cache::has($cacheKey)) {
                Cache::forget($cacheKey);
            }
        });
    }
}
