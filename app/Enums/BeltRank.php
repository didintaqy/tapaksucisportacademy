<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum BeltRank: string implements HasColor, HasLabel
{
    case SiswaDasar = 'siswa_dasar';
    case SiswaMelati1 = 'siswa_melati_1';
    case SiswaMelati2 = 'siswa_melati_2';
    case SiswaMelati3 = 'siswa_melati_3';
    case SiswaMelati4 = 'siswa_melati_4';
    case KaderDasar = 'kader_dasar';
    case KaderMuda = 'kader_muda';
    case KaderMadya = 'kader_madya';
    case KaderKepala = 'kader_kepala';
    case KaderUtama = 'kader_utama';
    case PendekarMuda = 'pendekar_muda';
    case PendekarMadya = 'pendekar_madya';
    case PendekarKepala = 'pendekar_kepala';
    case PendekarUtama = 'pendekar_utama';
    case PendekarBesar = 'pendekar_besar';

    public function getLabel(): ?string
    {
        return $this->label();
    }

    public function getColor(): string|array|null
    {
        return $this->color();
    }

    public function label(): string
    {
        return match ($this) {
            self::SiswaDasar => 'Siswa Dasar',
            self::SiswaMelati1 => 'Siswa Melati 1',
            self::SiswaMelati2 => 'Siswa Melati 2',
            self::SiswaMelati3 => 'Siswa Melati 3',
            self::SiswaMelati4 => 'Siswa Melati 4',
            self::KaderDasar => 'Kader Dasar',
            self::KaderMuda => 'Kader Muda',
            self::KaderMadya => 'Kader Madya',
            self::KaderKepala => 'Kader Kepala',
            self::KaderUtama => 'Kader Utama',
            self::PendekarMuda => 'Pendekar Muda',
            self::PendekarMadya => 'Pendekar Madya',
            self::PendekarKepala => 'Pendekar Kepala',
            self::PendekarUtama => 'Pendekar Utama',
            self::PendekarBesar => 'Pendekar Besar',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::SiswaDasar,
            self::SiswaMelati1,
            self::SiswaMelati2,
            self::SiswaMelati3,
            self::SiswaMelati4 => 'warning',
            self::KaderDasar,
            self::KaderMuda,
            self::KaderMadya,
            self::KaderKepala,
            self::KaderUtama => 'info',
            self::PendekarMuda,
            self::PendekarMadya,
            self::PendekarKepala,
            self::PendekarUtama,
            self::PendekarBesar => 'gray',
        };
    }

    public static function order(): array
    {
        return [
            self::SiswaDasar,
            self::SiswaMelati1,
            self::SiswaMelati2,
            self::SiswaMelati3,
            self::SiswaMelati4,
            self::KaderDasar,
            self::KaderMuda,
            self::KaderMadya,
            self::KaderKepala,
            self::KaderUtama,
            self::PendekarMuda,
            self::PendekarMadya,
            self::PendekarKepala,
            self::PendekarUtama,
            self::PendekarBesar,
        ];
    }

    public static function options(): array
    {
        $options = [];
        foreach (self::order() as $rank) {
            $options[$rank->value] = $rank->label();
        }

        return $options;
    }

    public function next(): ?self
    {
        $order = self::order();
        $currentIndex = array_search($this, $order, true);
        if ($currentIndex === false || $currentIndex === count($order) - 1) {
            return null;
        }

        return $order[$currentIndex + 1];
    }

    public function isBlackBelt(): bool
    {
        return in_array($this, [self::PendekarMuda, self::PendekarMadya, self::PendekarKepala, self::PendekarUtama, self::PendekarBesar], true);
    }
}
