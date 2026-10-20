<?php

namespace App\Enums;

enum PromotionResult: string
{
    case Lulus = 'lulus';
    case TidakLulus = 'tidak_lulus';
    case Ditunda = 'ditunda';

    public function label(): string
    {
        return match ($this) {
            self::Lulus => 'Lulus',
            self::TidakLulus => 'Tidak Lulus',
            self::Ditunda => 'Ditunda',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Lulus => 'success',
            self::TidakLulus => 'danger',
            self::Ditunda => 'warning',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Lulus => 'heroicon-o-check-circle',
            self::TidakLulus => 'heroicon-o-x-circle',
            self::Ditunda => 'heroicon-o-clock',
        };
    }
}
