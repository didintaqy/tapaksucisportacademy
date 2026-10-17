<?php

namespace App\Services\Payments;

use App\Contracts\PaymentGateway;
use InvalidArgumentException;

class PaymentGatewayManager
{
    /**
     * Cache driver yang sudah di-resolve.
     *
     * @var array<string, PaymentGateway>
     */
    protected array $resolved = [];

    public function driver(?string $name = null): PaymentGateway
    {
        $name ??= (string) config('payment.default', 'manual');

        return $this->resolved[$name] ??= $this->resolve($name);
    }

    protected function resolve(string $name): PaymentGateway
    {
        $config = config("payment.gateways.{$name}");

        if (blank($config)) {
            throw new InvalidArgumentException("Payment gateway [{$name}] belum dikonfigurasi.");
        }

        return match ($config['driver'] ?? $name) {
            'manual' => new ManualPaymentGateway($config),
            'midtrans' => new MidtransPaymentGateway($config),
            default => throw new InvalidArgumentException(
                "Driver payment gateway [{$config['driver']}] belum tersedia."
            ),
        };
    }
}
