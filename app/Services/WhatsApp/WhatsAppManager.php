<?php

namespace App\Services\WhatsApp;

use App\Contracts\WhatsAppGateway;
use InvalidArgumentException;

class WhatsAppManager
{
    /**
     * @var array<string, WhatsAppGateway>
     */
    protected array $resolved = [];

    public function driver(?string $name = null): WhatsAppGateway
    {
        $name ??= (string) config('whatsapp.default', 'log');

        return $this->resolved[$name] ??= $this->resolve($name);
    }

    protected function resolve(string $name): WhatsAppGateway
    {
        $config = config("whatsapp.drivers.{$name}");

        if (blank($config)) {
            throw new InvalidArgumentException("Driver WhatsApp [{$name}] belum dikonfigurasi.");
        }

        return match ($config['driver'] ?? $name) {
            'log' => new LogWhatsAppGateway($config),
            default => throw new InvalidArgumentException(
                "Driver WhatsApp [{$config['driver']}] belum tersedia."
            ),
        };
    }
}
