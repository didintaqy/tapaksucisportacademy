<?php

namespace App\Models;

use Database\Factories\GalleryFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Gallery extends Model
{
    /** @use HasFactory<GalleryFactory> */
    use HasFactory;

    use SoftDeletes;

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

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderByDesc('taken_at')->orderByDesc('id');
    }
}
