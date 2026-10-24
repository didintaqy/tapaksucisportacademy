<?php

namespace App\Models;

use App\Services\SiteSettings;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteSetting extends Model
{
    protected $fillable = [
        'data',
    ];

    protected function casts(): array
    {
        return [
            'data' => 'array',
        ];
    }

    protected static function booted(): void
    {
        $flush = function (): void {
            Cache::forget(SiteSettings::CACHE_KEY);
            app(SiteSettings::class)->flushMemory();
        };

        static::saved($flush);
        static::deleted($flush);
    }
}
