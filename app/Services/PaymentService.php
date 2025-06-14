<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\User;
use App\Models\Book;
use Unicodeveloper\Paystack\Facades\Paystack;

class PaymentService
{
    public function initializeLateReturnPayment(User $user, Book $book, int $daysLate)
    {
        $dailyRate = config('library.late_return_daily_rate', 100); // Default to 100 naira per day
        $amount = $daysLate * $dailyRate;

        $payment = Payment::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'amount' => $amount,
            'payment_type' => 'late_return',
            'status' => 'pending',
            'payment_reference' => 'LATE_' . uniqid(),
            'payment_details' => [
                'days_late' => $daysLate,
                'daily_rate' => $dailyRate,
            ],
        ]);

        $paymentData = [
            'amount' => $amount * 100, // Convert to kobo
            'email' => $user->email,
            'reference' => $payment->payment_reference,
            'callback_url' => route('payment.verify'),
            'metadata' => [
                'payment_id' => $payment->id,
                'type' => 'late_return',
            ],
        ];

        return Paystack::getAuthorizationUrl($paymentData)->redirectNow();
    }

    public function initializeLostBookPayment(User $user, Book $book, float $price)
    {
        $payment = Payment::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'amount' => $price,
            'payment_type' => 'lost_book',
            'status' => 'pending',
            'payment_reference' => 'LOST_' . uniqid(),
            'payment_details' => [
                'book_price' => $price,
            ],
        ]);

        $paymentData = [
            'amount' => $price * 100, // Convert to kobo
            'email' => $user->email,
            'reference' => $payment->payment_reference,
            'callback_url' => route('payment.verify'),
            'metadata' => [
                'payment_id' => $payment->id,
                'type' => 'lost_book',
            ],
        ];

        return Paystack::getAuthorizationUrl($paymentData)->redirectNow();
    }

    public function verifyPayment(string $reference): bool
    {
        $paymentDetails = Paystack::getPaymentData();

        if ($paymentDetails['status']) {
            $payment = Payment::where('payment_reference', $reference)->first();

            if ($payment) {
                $payment->update([
                    'status' => 'completed',
                    'payment_details' => array_merge($payment->payment_details ?? [], [
                        'payment_data' => $paymentDetails['data'],
                    ]),
                ]);

                return true;
            }
        }

        return false;
    }
}
