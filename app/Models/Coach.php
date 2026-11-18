<?php

namespace App\Models;

use App\Traits\OptimizesImages;
use Database\Factories\CoachFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Coach extends Model
{
    /** @use HasFactory<CoachFactory> */
    use HasFactory;

    use OptimizesImages;

    protected function imageAttributes(): array
    {
        return ['photo_path'];
    }

    protected function imageMaxDimensions(string $attribute): array
    {
        return [400, 400];
    }

    protected $fillable = [
        'user_id',
        'name',
        'title',
        'specialization',
        'bio',
        'photo_path',
        'experience_years',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'experience_years' => 'integer',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(TrainingSchedule::class, 'coach_ref_id');
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'coach_student')
            ->withPivot(['assigned_at', 'released_at'])
            ->withTimestamps();
    }

    public function getPhotoUrlAttribute(): ?string
    {
        if (blank($this->photo_path)) {
            return null;
        }

        if (Str::startsWith($this->photo_path, ['http://', 'https://', '//'])) {
            return $this->photo_path;
        }

        if (Str::startsWith($this->photo_path, 'images/')) {
            return asset($this->photo_path);
        }

        return url(Storage::disk('public')->url($this->photo_path));
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

    public function getExperienceLabelAttribute(): ?string
    {
        return $this->experience_years
            ? $this->experience_years.' tahun pengalaman'
            : null;
    }
}
