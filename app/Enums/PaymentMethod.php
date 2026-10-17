<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum PaymentMethod: string implements HasColor, HasIcon, HasLabel
{
    case Cash = 'cash';
    case Transfer = 'transfer';
    case Qris = 'qris';
    case Gateway = 'gateway';

    public function getLabel(): string
    {
        return match ($this) {
            self::Cash => 'Tunai',
            self::Transfer => 'Transfer Bank',
            self::Qris => 'QRIS',
            self::Gateway => 'Payment Gateway',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Cash => 'success',
            self::Transfer => 'info',
            self::Qris => 'warning',
            self::Gateway => 'primary',
        };
    }

    public function getIcon(): string|\BackedEnum|null
    {
        return match ($this) {
            self::Cash => 'heroicon-o-banknotes',
            self::Transfer => 'heroicon-o-building-library',
            self::Qris => 'heroicon-o-qr-code',
            self::Gateway => 'heroicon-o-credit-card',
        };
    }
}
