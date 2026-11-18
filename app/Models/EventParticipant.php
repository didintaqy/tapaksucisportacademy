<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventParticipant extends Model
{
    public const STATUSES = [
        'registered' => 'Terdaftar',
        'confirmed' => 'Terkonfirmasi',
        'attended' => 'Hadir',
        'cancelled' => 'Batal',
    ];

    protected $fillable = [
        'event_id',
        'student_id',
        'registered_at',
        'status',
        'fee_amount',
        'result_note',
    ];

    protected function casts(): array
    {
        return [
            'registered_at' => 'datetime',
            'fee_amount' => 'decimal:2',
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function scopeAttended(Builder $query): Builder
    {
        return $query->where('status', 'attended');
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }
}
