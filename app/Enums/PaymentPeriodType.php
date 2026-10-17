<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum PaymentPeriodType: string implements HasLabel
{
    case OneTime = 'one_time';
    case Monthly = 'monthly';
    case Event = 'event';

    public function getLabel(): string
    {
        return match ($this) {
            self::OneTime => 'Sekali Bayar',
            self::Monthly => 'Iuran Bulanan',
            self::Event => 'Kegiatan / Event',
        };
    }

    public function requiresPeriod(): bool
    {
        return $this === self::Monthly;
    }
}
