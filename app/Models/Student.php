<?php

namespace App\Models;

use Database\Factories\StudentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
    ];

    /** @use HasFactory<StudentFactory> */
    use HasFactory, softDeletes;

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

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
