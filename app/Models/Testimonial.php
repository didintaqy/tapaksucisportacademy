<?php

namespace App\Models;

use App\Enums\TestimonialStatus;
use Database\Factories\TestimonialFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Testimonial extends Model
{
    /** @use HasFactory<TestimonialFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'relation',
        'content',
        'rating',
        'status',
        'is_featured',
        'published_at',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'status' => TestimonialStatus::class,
            'is_featured' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', TestimonialStatus::Approved->value);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function getInitialsAttribute(): string
    {
        $words = preg_split('/\s+/', trim((string) $this->name)) ?: [];
        $words = array_values(array_filter($words));

        if ($words === []) {
            return 'TS';
        }

        $initials = mb_substr($words[0], 0, 1);

        if (count($words) > 1) {
            $initials .= mb_substr((string) end($words), 0, 1);
        }

        return mb_strtoupper($initials);
    }

    public function approve(): void
    {
        $this->forceFill([
            'status' => TestimonialStatus::Approved,
            'published_at' => $this->published_at ?? now(),
        ])->save();
    }

    public function reject(): void
    {
        $this->forceFill([
            'status' => TestimonialStatus::Rejected,
        ])->save();
    }
}
