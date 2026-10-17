<?php

namespace App\Http\Controllers;

use App\Models\Payment;

class PaymentReceiptController extends Controller
{
    /**
     * Menampilkan kwitansi pembayaran yang siap dicetak / diunduh PDF
     * melalui fitur "Print to PDF" pada browser.
     */
    public function show(Payment $payment)
    {
        $payment->load(['student', 'paymentType', 'creator']);

        return view('receipts.payment', [
            'payment' => $payment,
        ]);
    }
}
