<?php

namespace App\Services\Payments;

use App\Contracts\PaymentGateway;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Payment;

/**
 * Driver default tanpa provider eksternal.
 *
 * Tagihan ditandai "pending" dan orang tua bisa membayar dengan scan QRIS
 * (payload placeholder/dari konfigurasi). Konfirmasi akhir tetap dilakukan
 * admin sebelum status berubah menjadi "lunas".
 */
class ManualPaymentGateway implements PaymentGateway
{
    /**
     * @param  array<string, mixed>  $config
     */
    public function __construct(protected array $config = []) {}

    public function name(): string
    {
        return 'manual';
    }

    public function createCharge(Payment $payment): array
    {
        $reference = $payment->invoice_number;
        $qrString = $this->qrPayload($payment);

        $payment->forceFill([
            'status' => PaymentStatus::Pending,
            'payment_method' => PaymentMethod::Qris,
            'gateway_name' => $this->name(),
            'gateway_reference' => $reference,
            'gateway_payload' => [
                'order_id' => $reference,
                'qr_string' => $qrString,
                'qr_url' => null,
                'amount' => (float) $payment->amount,
                'merchant' => config('payment.merchant.name'),
            ],
        ])->save();

        return [
            'reference' => $reference,
            'qr_string' => $qrString,
            'qr_url' => null,
            'amount' => (float) $payment->amount,
            'expires_at' => now()->addDay(),
            'instructions' => [
                'Buka aplikasi mobile banking / e-wallet (GoPay, OVO, DANA, ShopeePay, dll).',
                'Pilih menu Scan / Bayar QRIS.',
                'Arahkan kamera ke QR code di halaman ini.',
                'Pastikan nominal dan nama siswa sesuai, lalu selesaikan pembayaran.',
                'Simpan bukti pembayaran. Status akan diperbarui otomatis / oleh admin.',
            ],
        ];
    }

    public function verifyCallback(array $payload, array $headers = []): bool
    {
        return true;
    }

    public function parseCallback(array $payload): array
    {
        return [
            'reference' => $payload['order_id'] ?? ($payload['reference'] ?? null),
            'status' => ($payload['status'] ?? null) === 'paid' ? PaymentStatus::Paid : PaymentStatus::Pending,
            'method' => PaymentMethod::Qris,
        ];
    }

    protected function qrPayload(Payment $payment): string
    {
        $configured = config('payment.merchant.qris_payload');

        if (filled($configured)) {
            return (string) $configured;
        }

        // Placeholder payload dinamis. Ganti dengan payload QRIS asli dari
        // provider (Midtrans/Xendit) ketika gateway sudah terhubung.
        return sprintf(
            'TAPAKSUCI-PAY|%s|%s|%.0f',
            $payment->invoice_number,
            $payment->student?->nickname ?? $payment->student_id,
            (float) $payment->amount,
        );
    }
}
