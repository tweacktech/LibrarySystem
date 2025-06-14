<?php

namespace App\Http\Controllers;

use App\Services\PaymentService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
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
