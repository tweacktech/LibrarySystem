<?php

namespace App\Console\Commands;

use App\Models\Payment;
use Illuminate\Console\Command;

class CheckPayments extends Command
{
    protected $signature = 'payments:check';
    protected $description = 'Check pending payments in the system';

    public function handle()
    {
        $pendingPayments = Payment::where('status', 'pending')->get();

        if ($pendingPayments->isEmpty()) {
            $this->info('No pending payments found.');
            return;
        }

        $this->info('Found ' . $pendingPayments->count() . ' pending payments:');

        foreach ($pendingPayments as $payment) {
            $this->line('----------------------------------------');
            $this->line('Reference: ' . $payment->payment_reference);
            $this->line('User: ' . $payment->user->name);
            $this->line('Book: ' . ($payment->book ? $payment->book->title : 'N/A'));
            $this->line('Amount: ₦' . number_format($payment->amount, 2));
            $this->line('Type: ' . $payment->payment_type);
            $this->line('Created: ' . $payment->created_at);
        }
    }
}
