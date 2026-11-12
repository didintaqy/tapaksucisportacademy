<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScoreCategory extends Model
{
    protected $fillable = [
        'min_score',
        'max_score',
        'label',
        'description',
        'color',
    ];

    protected function casts(): array
    {
        return [
            'min_score' => 'float',
            'max_score' => 'float',
        ];
    }
}
