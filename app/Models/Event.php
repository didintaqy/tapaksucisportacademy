<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'starts_at',
        'ends_at',
        'type',
        'is_public',
        'created_by',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'is_public' => 'boolean',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function participantRecords(): HasMany
    {
        return $this->hasMany(EventParticipant::class);
    }

    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'event_participants')
            ->withPivot(['status', 'fee_amount', 'result_note'])
            ->withTimestamps();
    }

    public function getTypeColorAttribute(): string
    {
        return match ($this->type) {
            'latihan' => 'primary',
            'kejuaraan' => 'danger',
            'ukt' => 'warning',
            'pengumuman' => 'info',
            'libur' => 'secondary',
            default => 'primary',
        };
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'latihan' => 'Latihan',
            'kejuaraan' => 'Kejuaraan',
            'ukt' => 'UKT',
            'pengumuman' => 'Pengumuman',
            'libur' => 'Libur',
            default => $this->type,
        };
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('starts_at', '>=', now());
    }

    public function scopePast($query)
    {
        return $query->where('starts_at', '<', now());
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeInMonth($query, int $month, int $year)
    {
        return $query->whereMonth('starts_at', $month)
            ->whereYear('starts_at', $year);
    }

    public function localStartsAt(): ?Carbon
    {
        return $this->starts_at?->copy()->tz(config('app.display_timezone'));
    }

    public function localEndsAt(): ?Carbon
    {
        return $this->ends_at?->copy()->tz(config('app.display_timezone'));
    }
}
