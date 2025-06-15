<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Filament\Notifications\Notification;

class PaymentController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function process($reference)
    {
        $payment = Payment::where('payment_reference', $reference)
            ->where('status', 'pending')
            ->firstOrFail();

        $paymentData = [
            'amount' => $payment->amount * 100, // Convert to kobo
            'email' => auth()->user()->email,
            'reference' => $payment->payment_reference,
            'callback_url' => route('payment.verify'),
            'metadata' => [
                'payment_id' => $payment->id,
                'type' => $payment->payment_type,
            ],
        ];

        return \Unicodeveloper\Paystack\Facades\Paystack::getAuthorizationUrl($paymentData)->redirectNow();
    }

    public function verify(Request $request)
    {
        $reference = $request->reference;

        if ($this->paymentService->verifyPayment($reference)) {
            return redirect()->route('dashboard')->with('success', 'Payment completed successfully');
        }

        return redirect()->route('dashboard')->with('error', 'Payment verification failed');
    }

    public function handleCallback(Request $request)
    {
        $reference = $request->reference;
        $payment = Payment::where('payment_reference', $reference)->first();

        if (!$payment) {
            return redirect()->route('filament.user.pages.payments')
                ->with('error', 'Payment not found');
        }

        // Verify payment with Paystack
        $paystack = new \Yabacon\Paystack(config('services.paystack.secret'));
        try {
            $tranx = $paystack->transaction->verify([
                'reference' => $reference,
            ]);

            if ($tranx->status && $tranx->data->status === 'success') {
                // Update payment status
                $payment->update([
                    'status' => 'completed',
                    'payment_details' => array_merge($payment->payment_details ?? [], [
                        'paystack_verification' => $tranx->data,
                    ]),
                ]);

                Notification::make()
                    ->title('Payment Successful')
                    ->body('Your payment has been processed successfully.')
                    ->success()
                    ->send();

                return redirect()->route('filament.user.pages.payments');
            }
        } catch (\Exception $e) {
            $payment->update([
                'status' => 'failed',
                'payment_details' => array_merge($payment->payment_details ?? [], [
                    'error' => $e->getMessage(),
                ]),
            ]);

            Notification::make()
                ->title('Payment Failed')
                ->body('There was an error processing your payment. Please try again.')
                ->danger()
                ->send();

            return redirect()->route('filament.user.pages.payments');
        }
    }
}
