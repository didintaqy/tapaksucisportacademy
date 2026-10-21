<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ProgramLevel: string implements HasColor, HasLabel
{
    case Beginner = 'pemula';
    case Intermediate = 'menengah';
    case Advanced = 'lanjutan';
    case All = 'semua';

    public function getLabel(): string
    {
        return match ($this) {
            self::Beginner => 'Pemula',
            self::Intermediate => 'Menengah',
            self::Advanced => 'Lanjutan',
            self::All => 'Semua Tingkat',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Beginner => 'info',
            self::Intermediate => 'warning',
            self::Advanced => 'danger',
            self::All => 'success',
        };
    }
}
