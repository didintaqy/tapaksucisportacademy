<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum PaymentStatus: string implements HasColor, HasIcon, HasLabel
{
    case Unpaid = 'unpaid';
    case Pending = 'pending';
    case Paid = 'paid';
    case Expired = 'expired';
    case Failed = 'failed';
    case Cancelled = 'cancelled';

    public function getLabel(): string
    {
        return match ($this) {
            self::Unpaid => 'Belum Lunas',
            self::Pending => 'Menunggu Pembayaran',
            self::Paid => 'Lunas',
            self::Expired => 'Kedaluwarsa',
            self::Failed => 'Gagal',
            self::Cancelled => 'Dibatalkan',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Unpaid => 'danger',
            self::Pending => 'warning',
            self::Paid => 'success',
            self::Expired => 'gray',
            self::Failed => 'danger',
            self::Cancelled => 'gray',
        };
    }

    public function getIcon(): string|\BackedEnum|null
    {
        return match ($this) {
            self::Paid => 'heroicon-o-check-circle',
            self::Pending => 'heroicon-o-clock',
            self::Unpaid => 'heroicon-o-exclamation-triangle',
            self::Expired => 'heroicon-o-clock',
            self::Failed => 'heroicon-o-exclamation-triangle',
            self::Cancelled => 'heroicon-o-x-circle',
        };
    }

    public function isPaid(): bool
    {
        return $this === self::Paid;
    }

    public function isPayable(): bool
    {
        return in_array($this, [self::Unpaid, self::Pending], true);
    }

    /**
     * Status yang dianggap belum selesai dibayar (untuk hitung tunggakan).
     *
     * @return array<int, self>
     */
    public static function outstanding(): array
    {
        return [self::Unpaid, self::Pending];
    }
}
