<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Branch extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'address',
        'phone',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Branch $branch): void {
            if (blank($branch->slug)) {
                $branch->slug = Str::slug((string) $branch->name);
            }
        });
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function trainingSchedules(): HasMany
    {
        return $this->hasMany(TrainingSchedule::class);
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(StudentRegistration::class);
    }

    public function programs(): BelongsToMany
    {
        return $this->belongsToMany(Program::class, 'program_branch');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Cari id cabang berdasarkan nama tanpa memicu event model.
     * Dipakai saat transisi dari kolom string `branch` ke `branch_id`.
     */
    public static function resolveId(?string $name): ?int
    {
        $name = trim((string) $name);

        if ($name === '') {
            return null;
        }

        $slug = Str::slug($name);

        if ($slug === '') {
            return null;
        }

        $existing = DB::table('branches')->where('slug', $slug)->value('id');

        if ($existing !== null) {
            return (int) $existing;
        }

        return (int) DB::table('branches')->insertGetId([
            'name' => $name,
            'slug' => $slug,
            'is_active' => true,
            'sort_order' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
