<?php

namespace App\Models;

use App\Enums\DayOfWeek;
use App\Traits\SyncsBranch;
use Carbon\Carbon;
use Database\Factories\TrainingScheduleFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrainingSchedule extends Model
{
    /** @use HasFactory<TrainingScheduleFactory> */
    use HasFactory;

    use SyncsBranch;

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
        'program_id',
        'branch',
        'branch_id',
        'day_of_week',
        'start_time',
        'end_time',
        'location',
        'coach_name',
        'coach_id',
        'coach_ref_id',
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

    public function coach(): BelongsTo
    {
        return $this->belongsTo(User::class, 'coach_id');
    }

    /**
     * Profil pelatih terpadu (sumber baru). `coach()` lama dipertahankan
     * sampai cutover selesai.
     */
    public function coachProfile(): BelongsTo
    {
        return $this->belongsTo(Coach::class, 'coach_ref_id');
    }

    public function branchRecord(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function getDayNameAttribute(): string
    {
        return self::DAYS[$this->day_of_week] ?? '-';
    }

    public function getDayAttribute(): ?DayOfWeek
    {
        return DayOfWeek::tryFrom($this->day_of_week);
    }

    public function getTimeRangeAttribute(): string
    {
        return substr($this->start_time, 0, 5).' - '.substr($this->end_time, 0, 5);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function getNextSessionDate(?Carbon $from = null): ?Carbon
    {
        $from = $from ?? now();
        $dayOfWeek = $this->day_of_week; // 1=Senin ... 7=Minggu

        // Carbon: 0=Minggu, 1=Senin, ..., 6=Sabtu
        $carbonDayOfWeek = $dayOfWeek === 7 ? 0 : $dayOfWeek;

        $next = $from->copy()->next($carbonDayOfWeek);

        // Jika hari ini adalah hari latihan dan belum lewat jam, return hari ini
        if ($from->dayOfWeek === $carbonDayOfWeek && $from->format('H:i') <= $this->start_time) {
            return $from->copy()->startOfDay();
        }

        return $next->startOfDay();
    }

    public function getPreviousSessionDate(?Carbon $from = null): ?Carbon
    {
        $from = $from ?? now();
        $dayOfWeek = $this->day_of_week;
        $carbonDayOfWeek = $dayOfWeek === 7 ? 0 : $dayOfWeek;

        return $from->copy()->previous($carbonDayOfWeek)->startOfDay();
    }
}
