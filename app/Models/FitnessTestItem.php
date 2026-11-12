<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FitnessTestItem extends Model
{
    protected $fillable = [
        'code',
        'key',
        'name',
        'fitness_component_id',
        'unit',
        'direction',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function component(): BelongsTo
    {
        return $this->belongsTo(FitnessComponent::class, 'fitness_component_id');
    }

    public function benchmarks(): HasMany
    {
        return $this->hasMany(FitnessBenchmark::class);
    }

    public function isLowerBetter(): bool
    {
        return strtoupper((string) $this->direction) === 'LOWER';
    }
}
