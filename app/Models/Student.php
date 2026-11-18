<?php

namespace App\Models;

use App\Traits\LogsActivity;
use App\Traits\OptimizesImages;
use App\Traits\SyncsBranch;
use Database\Factories\StudentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    protected $fillable = [
        'student_code',
        'full_name',
        'nickname',
        'birth_place',
        'birth_date',
        'nik',
        'family_card_number',
        'phone_number',
        'email',
        'gender',
        'belt_level',
        'address',
        'education',
        'photo',
        'branch',
        'branch_id',
        'program_id',
    ];

    /** @use HasFactory<StudentFactory> */
    use HasFactory, softDeletes;

    use LogsActivity, OptimizesImages, SyncsBranch;

    protected function imageAttributes(): array
    {
        return ['photo'];
    }

    protected function imageMaxDimensions(string $attribute): array
    {
        return [800, 800];
    }

    protected static function booted(): void
    {
        static::created(function (Student $student): void {
            if (blank($student->student_code)) {
                $student->forceFill([
                    'student_code' => 'SIS-'.str_pad((string) $student->id, 5, '0', STR_PAD_LEFT),
                ])->saveQuietly();
            }
        });
    }

    public function performances(): HasMany
    {
        return $this->hasMany(StudentPerformance::class);
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function branchRecord(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(StudentDocument::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function achievements(): HasMany
    {
        return $this->hasMany(Achievement::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function beltPromotions(): HasMany
    {
        return $this->hasMany(BeltPromotion::class);
    }
}
