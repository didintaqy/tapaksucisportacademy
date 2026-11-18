<?php

namespace App\Models;

use App\Enums\EnrollmentStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Enrollment extends Model
{
    protected $fillable = [
        'student_id',
        'program_id',
        'branch_id',
        'coach_id',
        'status',
        'started_at',
        'ended_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'status' => EnrollmentStatus::class,
            'started_at' => 'date',
            'ended_at' => 'date',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Enrollment $enrollment): void {
            $enrollment->status = $enrollment->status ?? EnrollmentStatus::Active;

            // `active_key` menegakkan "satu keanggotaan aktif per siswa".
            // NULL untuk baris non-aktif agar tidak saling bentrok.
            $enrollment->active_key = $enrollment->isActive()
                ? $enrollment->student_id
                : null;
        });
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function coach(): BelongsTo
    {
        return $this->belongsTo(Coach::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', EnrollmentStatus::Active->value)
            ->whereNull('ended_at');
    }

    public function isActive(): bool
    {
        return $this->status === EnrollmentStatus::Active && $this->ended_at === null;
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->status?->getLabel() ?? '-';
    }
}
