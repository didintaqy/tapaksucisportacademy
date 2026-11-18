<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum EnrollmentStatus: string implements HasColor, HasLabel
{
    case Active = 'active';
    case Cuti = 'cuti';
    case Berhenti = 'berhenti';
    case Lulus = 'lulus';

    public function getLabel(): string
    {
        return match ($this) {
            self::Active => 'Aktif',
            self::Cuti => 'Cuti',
            self::Berhenti => 'Berhenti',
            self::Lulus => 'Lulus',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Active => 'success',
            self::Cuti => 'warning',
            self::Berhenti => 'danger',
            self::Lulus => 'info',
        };
    }

    public function isActive(): bool
    {
        return $this === self::Active;
    }
}
