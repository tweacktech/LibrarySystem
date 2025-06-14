<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\Request;

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
}
