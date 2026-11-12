<?php

namespace App\Models;

use App\Services\SiteSettings;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic;

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
        static::saved(function (SiteSetting $setting) {
            Cache::forget(SiteSettings::CACHE_KEY);
            app(SiteSettings::class)->flushMemory();

            $data = (array) ($setting->data ?? []);
            $imageFields = [
                'identity.logo' => [300, 100],
                'identity.favicon' => [64, 64],
                'identity.page_header_image' => [1920, 600],
                'hero.image' => [1920, 1080],
                'about.image' => [1200, 800],
                'seo.image' => [1200, 630],
            ];

            foreach ($imageFields as $field => [$maxW, $maxH]) {
                $path = data_get($data, $field);
                if (blank($path) || str_starts_with((string) $path, 'http')) {
                    continue;
                }

                $full = Storage::disk('public')->path($path);
                if (! file_exists($full)) {
                    continue;
                }

                try {
                    $image = ImageManagerStatic::make($full);
                    if ($image->width() > $maxW || $image->height() > $maxH) {
                        $image->resize($maxW, $maxH, function ($constraint) {
                            $constraint->aspectRatio();
                            $constraint->upsize();
                        });
                    }

                    $ext = strtolower(pathinfo($full, PATHINFO_EXTENSION));
                    $quality = 85;
                    if (in_array($ext, ['jpg', 'jpeg', 'webp'], true)) {
                        $image->save($full, $quality);
                    } elseif ($ext === 'png') {
                        $image->save($full);
                    }
                } catch (\Throwable $e) {
                    report($e);
                }
            }
        });
        static::deleted(function (): void {
            Cache::forget(SiteSettings::CACHE_KEY);
            app(SiteSettings::class)->flushMemory();
        });
    }
}
