<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum CompetitionLevel: string implements HasLabel
{
    case Internal = 'internal';
    case Kota = 'kota';
    case Provinsi = 'provinsi';
    case Regional = 'regional';
    case Nasional = 'nasional';
    case Internasional = 'internasional';

    public function getLabel(): ?string
    {
        return $this->label();
    }

    public function label(): string
    {
        return match ($this) {
            self::Internal => 'Internal / Antar Cabang',
            self::Kota => 'Kota / Kabupaten',
            self::Provinsi => 'Provinsi / Daerah',
            self::Regional => 'Regional',
            self::Nasional => 'Nasional',
            self::Internasional => 'Internasional',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Internal => 'gray',
            self::Kota => 'info',
            self::Provinsi => 'primary',
            self::Regional => 'warning',
            self::Nasional => 'success',
            self::Internasional => 'danger',
        };
    }

    public static function options(): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }

        return $options;
    }
}
