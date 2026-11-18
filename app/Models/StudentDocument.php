<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentDocument extends Model
{
    public const TYPES = [
        'kk' => 'Kartu Keluarga',
        'akta' => 'Akta Kelahiran',
        'ijazah' => 'Ijazah',
        'foto' => 'Foto',
        'lain' => 'Lainnya',
    ];

    protected $fillable = [
        'student_id',
        'type',
        'file_path',
        'mime_type',
        'size_bytes',
        'uploaded_by',
        'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'size_bytes' => 'integer',
            'verified_at' => 'datetime',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }
}
