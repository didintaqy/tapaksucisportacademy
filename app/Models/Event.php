<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
}
