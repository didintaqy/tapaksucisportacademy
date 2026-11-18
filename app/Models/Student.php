<?php

namespace App\Models;

use App\Enums\EnrollmentStatus;
use App\Traits\LogsActivity;
use App\Traits\OptimizesImages;
use App\Traits\SyncsBranch;
use Database\Factories\StudentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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

            // Buat keanggotaan awal bila siswa langsung punya program.
            if (filled($student->program_id) && ! $student->enrollments()->exists()) {
                $student->enrollments()->create([
                    'program_id' => $student->program_id,
                    'branch_id' => $student->branch_id,
                    'status' => EnrollmentStatus::Active,
                    'started_at' => now()->toDateString(),
                ]);
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

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function activeEnrollment(): ?Enrollment
    {
        return $this->enrollments()->active()->latest('started_at')->first();
    }

    public function coaches(): BelongsToMany
    {
        return $this->belongsToMany(Coach::class, 'coach_student')
            ->withPivot(['assigned_at', 'released_at'])
            ->withTimestamps();
    }

    public function eventParticipations(): HasMany
    {
        return $this->hasMany(EventParticipant::class);
    }

    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'event_participants')
            ->withPivot(['status', 'fee_amount', 'result_note'])
            ->withTimestamps();
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
