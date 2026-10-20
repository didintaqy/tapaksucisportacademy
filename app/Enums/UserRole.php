<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum UserRole: string implements HasColor, HasLabel
{
    case Admin = 'admin';
    case Operator = 'operator';
    case Pelatih = 'pelatih';

    public function getLabel(): string
    {
        return match ($this) {
            self::Admin => 'Administrator',
            self::Operator => 'Operator',
            self::Pelatih => 'Pelatih',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Admin => 'danger',
            self::Operator => 'warning',
            self::Pelatih => 'info',
        };
    }
}
