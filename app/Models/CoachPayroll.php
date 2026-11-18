<?php

namespace App\Models;

use App\Enums\PayrollStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CoachPayroll extends Model
{
    protected $fillable = [
        'coach_id',
        'period',
        'sessions_count',
        'rate_type',
        'rate_amount',
        'bonus',
        'deduction',
        'total',
        'status',
        'paid_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'sessions_count' => 'integer',
            'rate_amount' => 'decimal:2',
            'bonus' => 'decimal:2',
            'deduction' => 'decimal:2',
            'total' => 'decimal:2',
            'status' => PayrollStatus::class,
            'paid_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (CoachPayroll $payroll): void {
            $payroll->recalculate();
        });
    }

    public function coach(): BelongsTo
    {
        return $this->belongsTo(Coach::class);
    }

    public function recalculate(): void
    {
        $base = $this->rate_type === 'per_month'
            ? (float) $this->rate_amount
            : $this->sessions_count * (float) $this->rate_amount;

        $this->total = max(0, $base + (float) $this->bonus - (float) $this->deduction);
    }

    public function markAsPaid(): void
    {
        $this->forceFill([
            'status' => PayrollStatus::Paid,
            'paid_at' => now(),
        ])->save();
    }

    public function isPaid(): bool
    {
        return $this->status === PayrollStatus::Paid;
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->status?->getLabel() ?? '-';
    }
}
