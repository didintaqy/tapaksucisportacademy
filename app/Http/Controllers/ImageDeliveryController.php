<?php

namespace App\Http\Controllers;

use App\Models\Coach;
use App\Models\Gallery;
use App\Models\Program;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManagerStatic as Image;

class ImageDeliveryController extends Controller
{
    /**
     * Serve an optimized variant (webp/avif) of a model's image.
     *
     * @param  string  $model  Model short name: gallery, program, coach
     * @param  int  $id  Model ID
     * @param  string  $variant  Desired format: webp|avif
     */
    public function __invoke(string $model, int $id, string $variant): Response
    {
        $allowedVariants = ['webp', 'avif'];
        if (! in_array($variant, $allowedVariants, true)) {
            abort(404);
        }

        $modelClass = $this->resolveModelClass($model);
        if (! $modelClass) {
            abort(404);
        }

        /** @var Model $instance */
        $instance = $modelClass::findOrFail($id);

        $path = $this->getImagePath($instance);
        if (! $path || ! file_exists($path)) {
            abort(404);
        }

        // Generate optimized image on the fly (cached by browser via headers)
        $image = Image::make($path)->encode($variant, 80);

        return response($image->encoded)
            ->header('Content-Type', "image/$variant")
            ->header('Cache-Control', 'public, max-age=31536000, immutable')
            ->header('Vary', 'Accept');
    }

    /**
     * Map short name to fully qualified model class.
     */
    protected function resolveModelClass(string $short): ?string
    {
        return match ($short) {
            'gallery' => Gallery::class,
            'program' => Program::class,
            'coach' => Coach::class,
            default => null,
        };
    }

    /**
     * Determine the absolute filesystem path for a model's image.
     */
    protected function getImagePath($model): ?string
    {
        // Models in this project expose getImagePath() or getImageUrlAttribute()
        if (method_exists($model, 'getImagePath')) {
            return $model->getImagePath();
        }

        // Fallback: use the stored attribute (image / cover_image / photo_path)
        $attribute = match (get_class($model)) {
            Gallery::class => 'image',
            Program::class => 'cover_image',
            Coach::class => 'photo_path',
            default => 'image',
        };

        $stored = $model->{$attribute} ?? null;
        if (blank($stored)) {
            return null;
        }

        // If it's a public/images/... reference
        if (Str::startsWith($stored, 'images/')) {
            $full = public_path($stored);

            return file_exists($full) ? $full : null;
        }

        // Otherwise assume public disk
        return Storage::disk('public')->path($stored);
    }
}
