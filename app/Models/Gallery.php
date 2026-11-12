<?php

namespace App\Models;

use App\Traits\OptimizesImages;
use Database\Factories\GalleryFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\ImageOptimizer\OptimizerChainFactory;

class Gallery extends Model
{
    /** @use HasFactory<GalleryFactory> */
    use HasFactory;

    use OptimizesImages;
    use SoftDeletes;

    protected function imageAttributes(): array
    {
        return ['image'];
    }

    protected function imageMaxDimensions(string $attribute): array
    {
        return [1600, 1200];
    }

    protected static function booted(): void
    {
        static::saved(function (Gallery $gallery) {
            if ($gallery->wasChanged('image') && $gallery->image) {
                $path = $gallery->getImagePath();
                if ($path && file_exists($path)) {
                    // Optimize the uploaded image in place (lossless)
                    $optimizerChain = OptimizerChainFactory::create()
                        ->setOptimizers([
                            'png' => ['pngquant', 'optipng'],
                            'jpg' => ['jpegoptim'],
                            'jpeg' => ['jpegoptim'],
                            'gif' => ['gifsicle'],
                            'svg' => ['svgo'],
                        ]);
                    $optimizerChain->optimize($path);
                }
            }
        });
    }

    protected $fillable = [
        'title',
        'category',
        'description',
        'image',
        'taken_at',
        'is_published',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'taken_at' => 'date',
            'is_published' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /**
     * URL gambar: mendukung path disk publik, folder theme images/, atau URL absolut.
     */
    public function getImageUrlAttribute(): string
    {
        if (blank($this->image)) {
            return '';
        }

        if (Str::startsWith($this->image, ['http://', 'https://', '//'])) {
            return $this->image;
        }

        if (Str::startsWith($this->image, 'images/')) {
            return asset($this->image);
        }

        return url(Storage::disk('public')->url($this->image));
    }

    /**
     * Get the absolute filesystem path for the stored image.
     */
    protected function getImagePath(): ?string
    {
        if (blank($this->image)) {
            return null;
        }

        // If it's a public/images/... reference, resolve to public_path
        if (Str::startsWith($this->image, 'images/')) {
            $path = public_path($this->image);

            return file_exists($path) ? $path : null;
        }

        // Otherwise assume it's stored on the public disk (storage/app/public/...)
        return Storage::disk('public')->path($this->image);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderByDesc('taken_at')->orderByDesc('id');
    }
}
