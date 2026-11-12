<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FitnessBenchmark extends Model
{
    protected $fillable = [
        'fitness_test_item_id',
        'gender',
        'age_min',
        'age_max',
        'target_value',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'age_min' => 'integer',
            'age_max' => 'integer',
            'target_value' => 'float',
            'is_active' => 'boolean',
        ];
    }

    public function testItem(): BelongsTo
    {
        return $this->belongsTo(FitnessTestItem::class, 'fitness_test_item_id');
    }
}
