<?php

namespace App\Services;

use Intervention\Image\ImageManagerStatic as Image;

/**
 * Optimasi gambar raster: hanya mengecilkan (tidak memperbesar) lalu
 * mengompres ulang sesuai format.
 *
 * Berkas non-raster (PDF, SVG, ICO) dan format yang encoder GD-nya tidak
 * tersedia akan dilewati tanpa error.
 */
class ImageOptimizer
{
    public const RASTER_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'avif'];

    public function supports(string $path): bool
    {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        if (! in_array($extension, self::RASTER_EXTENSIONS, true)) {
            return false;
        }

        if ($extension === 'webp' && ! function_exists('imagewebp')) {
            return false;
        }

        if ($extension === 'avif' && ! function_exists('imageavif')) {
            return false;
        }

        return true;
    }

    /**
     * @return bool True bila berkas berhasil diproses.
     */
    public function optimize(
        string $path,
        int $maxWidth = 1920,
        int $maxHeight = 1920,
        int $quality = 85,
    ): bool {
        if (! is_file($path) || ! $this->supports($path)) {
            return false;
        }

        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        try {
            $image = Image::make($path);

            if ($image->width() > $maxWidth || $image->height() > $maxHeight) {
                $image->resize($maxWidth, $maxHeight, function ($constraint): void {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            }

            match (true) {
                in_array($extension, ['jpg', 'jpeg', 'webp', 'avif'], true) => $image->save($path, $quality),
                // PNG: parameter kualitas adalah level kompresi 0-9.
                $extension === 'png' => $image->save($path, min($quality, 9)),
                default => $image->save($path),
            };

            return true;
        } catch (\Throwable $e) {
            // Jangan gagalkan penyimpanan model/settings bila optimasi gagal.
            report($e);

            return false;
        }
    }
}
