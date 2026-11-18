<?php

namespace App\Traits;

use App\Services\ImageOptimizer;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait OptimizesImages
{
    /**
     * Daftar atribut (kolom) yang berisi path gambar untuk dioptimalkan.
     *
     * @return array<int, string>
     */
    protected function imageAttributes(): array
    {
        return [];
    }

    /**
     * Dimensi maksimum (lebar, tinggi) untuk sebuah atribut gambar.
     *
     * @return array{0: int, 1: int}
     */
    protected function imageMaxDimensions(string $attribute): array
    {
        return [1920, 1920];
    }

    /**
     * Kualitas kompresi (0-100) untuk sebuah atribut gambar.
     */
    protected function imageQuality(string $attribute): int
    {
        return 85;
    }

    protected static function bootOptimizesImages(): void
    {
        static::saved(function ($model) {
            $optimizer = app(ImageOptimizer::class);

            foreach ($model->imageAttributes() as $attribute) {
                if (! $model->wasChanged($attribute)) {
                    continue;
                }

                $stored = $model->{$attribute} ?? null;

                if (blank($stored) || Str::startsWith((string) $stored, ['http://', 'https://', '//'])) {
                    continue;
                }

                $path = $model->resolveImagePath((string) $stored);

                if (! $path) {
                    continue;
                }

                [$maxWidth, $maxHeight] = $model->imageMaxDimensions($attribute);

                $optimizer->optimize($path, $maxWidth, $maxHeight, $model->imageQuality($attribute));
            }
        });
    }

    /**
     * Resolve path filesystem absolut dari path yang tersimpan.
     */
    protected function resolveImagePath(string $stored): ?string
    {
        if (Str::startsWith($stored, 'images/')) {
            $full = public_path($stored);

            return file_exists($full) ? $full : null;
        }

        if (method_exists($this, 'getImagePath')) {
            return $this->getImagePath();
        }

        return Storage::disk('public')->path($stored);
    }
}
