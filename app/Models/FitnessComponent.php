<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FitnessComponent extends Model
{
    protected $fillable = [
        'code',
        'name',
        'weight',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'weight' => 'float',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function testItems(): HasMany
    {
        return $this->hasMany(FitnessTestItem::class)->orderBy('sort_order');
    }
}
