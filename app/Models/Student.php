<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    protected $fillable = [
        'full_name',
        'nickname',
        'birth_place',
        'birth_date',
        'nik',
        'family_card_number',
        'phone_number',
        'gender',
        'belt_level',
        'address',
        'education',
        'photo',
    ]; 

    /** @use HasFactory<\Database\Factories\StudentFactory> */
    use HasFactory, softDeletes;

    public function performances(): HasMany
    {
        return $this->hasMany(StudentPerformance::class);
    }
}
