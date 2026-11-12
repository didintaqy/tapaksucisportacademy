<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManagerStatic as Image;

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
            foreach ($model->imageAttributes() as $attribute) {
                if (! $model->wasChanged($attribute)) {
                    continue;
                }

                $stored = $model->{$attribute} ?? null;
                if (blank($stored) || Str::startsWith((string) $stored, ['http://', 'https://', '//'])) {
                    continue;
                }

                $path = $model->resolveImagePath((string) $stored);
                if (! $path || ! file_exists($path)) {
                    continue;
                }

                try {
                    [$maxWidth, $maxHeight] = $model->imageMaxDimensions($attribute);
                    $quality = $model->imageQuality($attribute);

                    $image = Image::make($path);

                    // Resize (hanya mengecilkan, tidak memperbesar)
                    if ($image->width() > $maxWidth || $image->height() > $maxHeight) {
                        $image->resize($maxWidth, $maxHeight, function ($constraint) {
                            $constraint->aspectRatio();
                            $constraint->upsize();
                        });
                    }

                    // Kompres sesuai ekstensi
                    $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
                    if (in_array($extension, ['jpg', 'jpeg'], true)) {
                        $image->save($path, $quality);
                    } elseif ($extension === 'png') {
                        // PNG: simpan dengan kompresi maksimal (level 9) via quality param
                        $image->save($path, min($quality, 9));
                    } elseif ($extension === 'webp') {
                        $image->save($path, $quality);
                    } else {
                        $image->save($path);
                    }
                } catch (\Throwable $e) {
                    // Jangan gagalkan penyimpanan model bila optimasi gagal
                    report($e);
                }
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
