<?php

namespace App\Http\Controllers;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Services\Payments\PaymentGatewayManager;
use Illuminate\Http\Request;

class PaymentCallbackController extends Controller
{
    public function __construct(protected PaymentGatewayManager $gateways) {}

    /**
     * Endpoint callback / webhook dari payment gateway.
     *
     * Memverifikasi signature, mencocokkan tagihan berdasarkan order_id,
     * lalu memperbarui status pembayaran secara otomatis.
     */
    public function handle(Request $request)
    {
        $payload = $request->all();
        $headers = $request->headers->all();

        $gateway = $this->gateways->driver();

        if (! $gateway->verifyCallback($payload, $headers)) {
            return response()->json(['status' => 'invalid signature'], 401);
        }

        $parsed = $gateway->parseCallback($payload);
        $reference = $parsed['reference'] ?? null;

        if (blank($reference)) {
            return response()->json(['status' => 'ignored'], 202);
        }

        $payment = Payment::query()
            ->where('invoice_number', $reference)
            ->orWhere('gateway_reference', $reference)
            ->first();

        if (! $payment) {
            return response()->json(['status' => 'not found'], 404);
        }

        $status = $parsed['status'];
        $method = $parsed['method'] ?? $payment->payment_method;

        $payment->forceFill([
            'gateway_payload' => array_merge(
                (array) $payment->gateway_payload,
                ['last_callback' => $payload],
            ),
        ])->save();

        if ($status === PaymentStatus::Paid) {
            if (! $payment->isPaid()) {
                $payment->markAsPaid($method, now(), 'Konfirmasi otomatis payment gateway');
            }
        } elseif ($payment->status !== $status) {
            $payment->changeStatus(
                $status,
                'Pembaruan status dari payment gateway',
                ['gateway' => $payment->gateway_name, 'gateway_status' => data_get($payload, 'transaction_status')],
            );
        }

        return response()->json(['status' => 'ok']);
    }
}
