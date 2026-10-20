<?php

namespace App\Enums;

enum BeltRank: string
{
    case Putih = 'putih';
    case Kuning = 'kuning';
    case Hijau = 'hijau';
    case Biru = 'biru';
    case Merah = 'merah';
    case Coklat = 'coklat';
    case Hitam1 = 'hitam_1';
    case Hitam2 = 'hitam_2';
    case Hitam3 = 'hitam_3';
    case Hitam4 = 'hitam_4';
    case Hitam5 = 'hitam_5';

    public function label(): string
    {
        return match ($this) {
            self::Putih => 'Putih',
            self::Kuning => 'Kuning',
            self::Hijau => 'Hijau',
            self::Biru => 'Biru',
            self::Merah => 'Merah',
            self::Coklat => 'Coklat',
            self::Hitam1 => 'Hitam 1 Dan',
            self::Hitam2 => 'Hitam 2 Dan',
            self::Hitam3 => 'Hitam 3 Dan',
            self::Hitam4 => 'Hitam 4 Dan',
            self::Hitam5 => 'Hitam 5 Dan',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Putih => 'gray',
            self::Kuning => 'warning',
            self::Hijau => 'success',
            self::Biru => 'info',
            self::Merah => 'danger',
            self::Coklat => 'orange',
            self::Hitam1,
            self::Hitam2,
            self::Hitam3,
            self::Hitam4,
            self::Hitam5 => 'dark',
        };
    }

    public static function order(): array
    {
        return [
            self::Putih,
            self::Kuning,
            self::Hijau,
            self::Biru,
            self::Merah,
            self::Coklat,
            self::Hitam1,
            self::Hitam2,
            self::Hitam3,
            self::Hitam4,
            self::Hitam5,
        ];
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
        return in_array($this, [self::Hitam1, self::Hitam2, self::Hitam3, self::Hitam4, self::Hitam5], true);
    }
}
