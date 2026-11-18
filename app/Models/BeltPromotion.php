<?php

namespace App\Models;

use App\Enums\BeltRank;
use App\Enums\PromotionResult;
use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BeltPromotion extends Model
{
    use HasFactory;
    use LogsActivity;

    protected $fillable = [
        'student_id',
        'examined_by',
        'exam_date',
        'current_belt',
        'target_belt',
        'result',
        'notes',
        'examiner_notes',
        'certificate_issued_at',
        'certificate_number',
    ];

    protected $casts = [
        'exam_date' => 'date',
        'certificate_issued_at' => 'date',
        'result' => PromotionResult::class,
    ];

    protected static function booted(): void
    {
        static::saving(function (BeltPromotion $promotion): void {
            if ($promotion->result !== PromotionResult::Lulus) {
                return;
            }

            if (blank($promotion->certificate_number)) {
                $promotion->certificate_number = $promotion->generateCertificateNumber();
            }

            if (blank($promotion->certificate_issued_at)) {
                $promotion->certificate_issued_at = now();
            }
        });
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function examiner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'examined_by');
    }

    public function getCurrentBeltLabelAttribute(): string
    {
        return BeltRank::tryFrom($this->current_belt)?->label() ?? $this->current_belt;
    }

    public function getTargetBeltLabelAttribute(): string
    {
        return BeltRank::tryFrom($this->target_belt)?->label() ?? $this->target_belt;
    }

    public function getResultLabelAttribute(): string
    {
        return $this->result?->label() ?? $this->result;
    }

    public function getResultColorAttribute(): string
    {
        return $this->result?->color() ?? 'gray';
    }

    public function getResultIconAttribute(): string
    {
        return $this->result?->icon() ?? 'heroicon-o-question-mark-circle';
    }

    public function scopePassed($query)
    {
        return $query->where('result', PromotionResult::Lulus);
    }

    public function scopeFailed($query)
    {
        return $query->where('result', PromotionResult::TidakLulus);
    }

    public function scopePending($query)
    {
        return $query->where('result', PromotionResult::Ditunda);
    }

    public function scopeForStudent($query, int $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function generateCertificateNumber(): string
    {
        $year = now()->year;
        $count = self::whereYear('created_at', $year)->count() + 1;

        return sprintf('UKT-%d-%04d', $year, $count);
    }
}
