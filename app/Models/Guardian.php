<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Guardian extends Model
{
    public const RELATIONS = [
        'ayah' => 'Ayah',
        'ibu' => 'Ibu',
        'wali' => 'Wali',
    ];

    protected $fillable = [
        'user_id',
        'name',
        'phone',
        'email',
        'occupation',
        'address',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'guardian_student')
            ->withPivot(['relation', 'is_primary'])
            ->withTimestamps();
    }

    public function primaryStudent(): ?Student
    {
        return $this->students()->wherePivot('is_primary', true)->first();
    }
}
