<?php

namespace App\Models;

use App\Enums\ProgramLevel;
use App\Traits\OptimizesImages;
use Database\Factories\ProgramFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Program extends Model
{
    /** @use HasFactory<ProgramFactory> */
    use HasFactory, SoftDeletes;

    use OptimizesImages;

    protected function imageAttributes(): array
    {
        return ['cover_image'];
    }

    protected function imageMaxDimensions(string $attribute): array
    {
        return [1200, 800];
    }

    protected $fillable = [
        'name',
        'slug',
        'short_description',
        'description',
        'cover_image',
        'age_min',
        'age_max',
        'level',
        'session_duration_minutes',
        'sessions_per_week',
        'monthly_fee',
        'branches',
        'is_active',
        'is_featured',
        'sort_order',
        'meta_title',
        'meta_description',
    ];

    protected function casts(): array
    {
        return [
            'age_min' => 'integer',
            'age_max' => 'integer',
            'level' => ProgramLevel::class,
            'session_duration_minutes' => 'integer',
            'sessions_per_week' => 'integer',
            'monthly_fee' => 'decimal:2',
            'branches' => 'array',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Program $program): void {
            if (blank($program->slug)) {
                $program->slug = Str::slug($program->name);
            }
        });
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(TrainingSchedule::class);
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(StudentRegistration::class);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Daftar cabang yang sudah dikenal, diambil dari program, jadwal, dan siswa.
     *
     * @return array<string, string>
     */
    public static function branchOptions(): array
    {
        $branches = collect()
            ->merge(static::query()->pluck('branches')->flatten())
            ->merge(TrainingSchedule::query()->whereNotNull('branch')->distinct()->pluck('branch'))
            ->merge(Student::query()->whereNotNull('branch')->distinct()->pluck('branch'))
            ->filter(fn ($branch): bool => filled($branch))
            ->unique()
            ->sort()
            ->values();

        return $branches->combine($branches)->all();
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function scopeForBranch(Builder $query, ?string $branch): Builder
    {
        if (blank($branch)) {
            return $query;
        }

        return $query->whereJsonContains('branches', $branch);
    }

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

    public function getAgeRangeLabelAttribute(): string
    {
        if ($this->age_min && $this->age_max) {
            return "{$this->age_min}–{$this->age_max} tahun";
        }

        if ($this->age_min) {
            return "{$this->age_min}+ tahun";
        }

        if ($this->age_max) {
            return "≤ {$this->age_max} tahun";
        }

        return 'Semua usia';
    }

    public function getDurationLabelAttribute(): string
    {
        $parts = [];

        if ($this->session_duration_minutes) {
            $parts[] = $this->session_duration_minutes.' menit/sesi';
        }

        if ($this->sessions_per_week) {
            $parts[] = $this->sessions_per_week.'×/minggu';
        }

        return $parts ? implode(' · ', $parts) : '—';
    }

    public function getMonthlyFeeLabelAttribute(): string
    {
        return $this->monthly_fee
            ? 'Rp '.number_format((float) $this->monthly_fee, 0, ',', '.')
            : 'Hubungi admin';
    }
}
