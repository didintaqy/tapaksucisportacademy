<?php

namespace App\Models;

use App\Enums\PostStatus;
use Database\Factories\PostFactory;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Post extends Model
{
    /** @use HasFactory<PostFactory> */
    use HasFactory;

    use SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'cover_image',
        'category_id',
        'author_id',
        'status',
        'published_at',
        'is_featured',
        'views',
    ];

    protected function casts(): array
    {
        return [
            'status' => PostStatus::class,
            'published_at' => 'datetime',
            'is_featured' => 'boolean',
            'views' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Post $post): void {
            if (blank($post->slug)) {
                $post->slug = Str::slug($post->title);
            }
        });

        static::created(function (Post $post): void {
            if ($post->status === PostStatus::Draft && $post->author->role !== 'admin') {
                Notification::make()
                    ->title('Artikel Baru Diajukan')
                    ->body("Artikel \"{$post->title}\" menunggu review.")
                    ->icon('heroicon-o-pencil-square')
                    ->sendToDatabase(User::where('role', 'admin')->get());
            }
        });
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function categoryRelation(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function getCategoryAttribute(): ?Category
    {
        return $this->categoryRelation;
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    /**
     * URL gambar sampul: mendukung path disk publik, folder theme images/, atau URL absolut.
     */
    public function getCoverUrlAttribute(): string
    {
        if (blank($this->cover_image)) {
            return '';
        }

        if (Str::startsWith($this->cover_image, ['http://', 'https://', '//'])) {
            return $this->cover_image;
        }

        if (Str::startsWith($this->cover_image, 'images/')) {
            return asset($this->cover_image);
        }

        return url(Storage::disk('public')->url($this->cover_image));
    }

    public function getAuthorNameAttribute(): string
    {
        return $this->author?->name ?? 'Admin';
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('status', PostStatus::Published->value)
            ->where(function (Builder $query): void {
                $query->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            });
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderByDesc('published_at')->orderByDesc('id');
    }

    public function isPublished(): bool
    {
        return $this->status === PostStatus::Published;
    }
}
