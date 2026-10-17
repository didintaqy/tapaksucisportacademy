<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum PostStatus: string implements HasColor, HasIcon, HasLabel
{
    case Draft = 'draft';
    case Published = 'published';

    public function getLabel(): string
    {
        return match ($this) {
            self::Draft => 'Draf',
            self::Published => 'Terbit',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Draft => 'gray',
            self::Published => 'success',
        };
    }

    public function getIcon(): string|\BackedEnum|null
    {
        return match ($this) {
            self::Draft => 'heroicon-o-pencil-square',
            self::Published => 'heroicon-o-check-circle',
        };
    }
}
