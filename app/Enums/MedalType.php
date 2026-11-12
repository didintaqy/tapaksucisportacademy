<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum MedalType: string implements HasLabel
{
    case Emas = 'emas';
    case Perak = 'perak';
    case Perunggu = 'perunggu';
    case JuaraUmum = 'juara_umum';
    case Partisipan = 'partisipan';

    public function getLabel(): ?string
    {
        return $this->label();
    }

    public function label(): string
    {
        return match ($this) {
            self::Emas => 'Medali Emas',
            self::Perak => 'Medali Perak',
            self::Perunggu => 'Medali Perunggu',
            self::JuaraUmum => 'Juara Umum',
            self::Partisipan => 'Finalis / Partisipan',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Emas => 'warning',
            self::Perak => 'gray',
            self::Perunggu => 'info',
            self::JuaraUmum => 'success',
            self::Partisipan => 'primary',
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
