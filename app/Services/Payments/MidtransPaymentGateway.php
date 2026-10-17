<?php

namespace App\Services\Payments;

use App\Contracts\PaymentGateway;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Payment;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Driver payment gateway Midtrans (Core API - QRIS).
 *
 * Membuat QR dinamis via endpoint /v2/charge lalu menampilkan QRIS ke orang
 * tua. Status pembayaran diperbarui otomatis melalui webhook (simulator
 * Midtrans) yang diverifikasi menggunakan signature SHA-512.
 *
 * @see https://docs.midtrans.com/reference/qris-1
 */
class MidtransPaymentGateway implements PaymentGateway
{
    /**
     * @param  array<string, mixed>  $config
     */
    public function __construct(protected array $config = []) {}

    public function name(): string
    {
        return 'midtrans';
    }

    public function createCharge(Payment $payment): array
    {
        $this->ensureConfigured();

        $orderId = $payment->invoice_number;
        $grossAmount = (int) round((float) $payment->amount);

        $response = Http::withBasicAuth($this->serverKey(), '')
            ->acceptJson()
            ->post($this->baseUrl().'/v2/charge', [
                'payment_type' => 'qris',
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => $grossAmount,
                ],
                'qris' => [
                    'acquirer' => 'gopay',
                ],
                'customer_details' => [
                    'first_name' => $payment->student?->full_name ?? 'Siswa',
                    'phone' => $payment->student?->phone_number,
                ],
            ]);

        if ($response->failed()) {
            throw new RuntimeException('Gagal membuat QRIS Midtrans: '.$response->body());
        }

        $data = $response->json() ?? [];

        $action = collect($data['actions'] ?? [])->firstWhere('name', 'generate-qr-code');
        $qrUrl = $action['url'] ?? null;
        $qrString = $data['qr_string'] ?? null;
        $expiresAt = isset($data['expiry_time'])
            ? Carbon::parse($data['expiry_time'])
            : now()->addMinutes(15);

        $payment->forceFill([
            'status' => PaymentStatus::Pending,
            'payment_method' => PaymentMethod::Qris,
            'gateway_name' => $this->name(),
            'gateway_reference' => $data['transaction_id'] ?? $orderId,
            'gateway_payload' => [
                'order_id' => $orderId,
                'transaction_id' => $data['transaction_id'] ?? null,
                'qr_string' => $qrString,
                'qr_url' => $qrUrl,
                'expires_at' => $expiresAt->toIso8601String(),
                'raw' => $data,
            ],
        ])->save();

        return [
            'reference' => $data['transaction_id'] ?? $orderId,
            'qr_string' => $qrString,
            'qr_url' => $qrUrl,
            'amount' => (float) $payment->amount,
            'expires_at' => $expiresAt,
            'instructions' => [
                'Buka aplikasi mobile banking / e-wallet (GoPay, OVO, DANA, ShopeePay, dll).',
                'Pilih menu Scan / Bayar QRIS.',
                'Arahkan kamera ke QR code di halaman ini.',
                'Pastikan nominal dan nama siswa sesuai, lalu selesaikan pembayaran.',
                'Status tagihan akan otomatis berubah menjadi LUNAS setelah pembayaran berhasil.',
            ],
        ];
    }

    public function verifyCallback(array $payload, array $headers = []): bool
    {
        $orderId = $payload['order_id'] ?? null;
        $statusCode = $payload['status_code'] ?? null;
        $grossAmount = $payload['gross_amount'] ?? null;
        $signature = $payload['signature_key'] ?? null;

        if (blank($orderId) || blank($statusCode) || blank($grossAmount) || blank($signature)) {
            return false;
        }

        $expected = hash('sha512', $orderId.$statusCode.$grossAmount.$this->serverKey());

        return hash_equals($expected, (string) $signature);
    }

    public function parseCallback(array $payload): array
    {
        $transactionStatus = $payload['transaction_status'] ?? null;
        $fraudStatus = $payload['fraud_status'] ?? null;

        $status = match ($transactionStatus) {
            'settlement' => PaymentStatus::Paid,
            'capture' => $fraudStatus === 'accept' ? PaymentStatus::Paid : PaymentStatus::Pending,
            'pending' => PaymentStatus::Pending,
            'deny' => PaymentStatus::Failed,
            'cancel' => PaymentStatus::Cancelled,
            'expire' => PaymentStatus::Expired,
            'refund', 'partial_refund' => PaymentStatus::Cancelled,
            default => PaymentStatus::Pending,
        };

        return [
            'reference' => $payload['order_id'] ?? null,
            'status' => $status,
            'method' => PaymentMethod::Qris,
        ];
    }

    protected function baseUrl(): string
    {
        return ($this->config['is_production'] ?? false)
            ? 'https://api.midtrans.com'
            : 'https://api.sandbox.midtrans.com';
    }

    protected function serverKey(): string
    {
        return (string) ($this->config['server_key'] ?? '');
    }

    protected function ensureConfigured(): void
    {
        if (blank($this->serverKey())) {
            throw new RuntimeException('Server key Midtrans belum dikonfigurasi (MIDTRANS_SERVER_KEY).');
        }
    }
}
