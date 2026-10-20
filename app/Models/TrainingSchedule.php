<?php

namespace App\Models;

use Database\Factories\TrainingScheduleFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingSchedule extends Model
{
    /** @use HasFactory<TrainingScheduleFactory> */
    use HasFactory;

    public const DAYS = [
        1 => 'Senin',
        2 => 'Selasa',
        3 => 'Rabu',
        4 => 'Kamis',
        5 => 'Jumat',
        6 => 'Sabtu',
        7 => 'Minggu',
    ];

    protected $fillable = [
        'class_name',
        'branch',
        'day_of_week',
        'start_time',
        'end_time',
        'location',
        'coach_name',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'day_of_week' => 'integer',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function getDayNameAttribute(): string
    {
        return self::DAYS[$this->day_of_week] ?? '-';
    }

    public function getTimeRangeAttribute(): string
    {
        return substr($this->start_time, 0, 5).' - '.substr($this->end_time, 0, 5);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
