<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CoachRate extends Model
{
    public const TYPES = [
        'per_session' => 'Per Sesi',
        'per_month' => 'Per Bulan',
    ];

    protected $fillable = [
        'coach_id',
        'rate_type',
        'amount',
        'effective_from',
        'effective_to',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'effective_from' => 'date',
            'effective_to' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function coach(): BelongsTo
    {
        return $this->belongsTo(Coach::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeEffectiveOn(Builder $query, string $date): Builder
    {
        return $query
            ->whereDate('effective_from', '<=', $date)
            ->where(function (Builder $inner) use ($date): void {
                $inner->whereNull('effective_to')
                    ->orWhereDate('effective_to', '>=', $date);
            });
    }

    public function getRateTypeLabelAttribute(): string
    {
        return self::TYPES[$this->rate_type] ?? $this->rate_type;
    }
}
