<?php

namespace App\Contracts;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Payment;
use Illuminate\Support\Carbon;

interface PaymentGateway
{
    /**
     * Nama driver gateway (mis. "manual", "midtrans").
     */
    public function name(): string;

    /**
     * Membuat transaksi/charge untuk sebuah tagihan pembayaran.
     *
     * @return array{
     *     reference: string,
     *     qr_string: string|null,
     *     qr_url: string|null,
     *     amount: float,
     *     expires_at: Carbon|null,
     *     instructions: array<int, string>,
     * }
     */
    public function createCharge(Payment $payment): array;

    /**
     * Memverifikasi callback/webhook dari provider.
     *
     * @param  array<string, mixed>  $payload
     * @param  array<string, mixed>  $headers
     */
    public function verifyCallback(array $payload, array $headers = []): bool;

    /**
     * Menerjemahkan payload callback menjadi status internal.
     *
     * @param  array<string, mixed>  $payload
     * @return array{reference: string|null, status: PaymentStatus, method: PaymentMethod|null}
     */
    public function parseCallback(array $payload): array;
}
