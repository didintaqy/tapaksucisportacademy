<?php

namespace App\Models;

use App\Enums\RegistrationStatus;
use Database\Factories\StudentRegistrationFactory;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentRegistration extends Model
{
    /** @use HasFactory<StudentRegistrationFactory> */
    use HasFactory;

    public const GENDERS = [
        'L' => 'Laki-laki',
        'P' => 'Perempuan',
    ];

    protected $fillable = [
        'full_name',
        'nickname',
        'gender',
        'birth_place',
        'birth_date',
        'education',
        'parent_name',
        'phone',
        'email',
        'address',
        'branch',
        'notes',
        'status',
        'admin_notes',
        'reviewed_by',
        'reviewed_at',
    ];

    protected static function booted(): void
    {
        static::created(function (StudentRegistration $registration): void {
            Notification::make()
                ->title('Pendaftaran Siswa Baru')
                ->body("{$registration->full_name} mendaftar sebagai siswa baru.")
                ->icon('heroicon-o-user-plus')
                ->sendToDatabase(User::whereIn('role', ['admin', 'operator'])->get());
        });
    }

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'status' => RegistrationStatus::class,
            'reviewed_at' => 'datetime',
        ];
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function scopeNew(Builder $query): Builder
    {
        return $query->where('status', RegistrationStatus::New->value);
    }

    public function markAs(RegistrationStatus $status, User $user): void
    {
        $this->forceFill([
            'status' => $status,
            'reviewed_by' => $user->id,
            'reviewed_at' => now(),
        ])->save();
    }
}
