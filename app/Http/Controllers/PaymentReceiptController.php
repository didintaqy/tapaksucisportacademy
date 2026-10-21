<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Support\Facades\Gate;

class PaymentReceiptController extends Controller
{
    /**
     * Menampilkan kwitansi pembayaran yang siap dicetak / diunduh PDF
     * melalui fitur "Print to PDF" pada browser.
     */
    public function show(Payment $payment)
    {
        Gate::authorize('view', $payment);

        $payment->load(['student', 'paymentType', 'creator']);

        return view('receipts.payment', [
            'payment' => $payment,
        ]);
    }
}
