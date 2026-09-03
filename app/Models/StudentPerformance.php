<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentPerformance extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terhubung (opsional jika sesuai konvensi plural).
     *
     * @var string
     */
    protected $table = 'user_performances';

    /**
     * Atribut yang dapat diisi secara massal (mass assignable).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'student_id',
        'resting_heart_rate',
        'max_heart_rate',
        'training_heart_rate',
        'height',
        'weight',
        'sit_and_reach',
        'push_up_1_min',
        'sit_up_2_min',
        'core_stability_level',
        'triple_hop_right',
        'triple_hop_left',
        'illinois_agility',
        'rast_average_power',
        'rast_fatigue_index',
        'beep_test_vo2max',
    ];

    /**
     * Relasi ke model Student (Setiap performa milik satu siswa).
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}