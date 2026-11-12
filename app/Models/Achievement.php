<?php

namespace App\Models;

use App\Enums\CompetitionLevel;
use App\Enums\MedalType;
use Database\Factories\AchievementFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Achievement extends Model
{
    /** @use HasFactory<AchievementFactory> */
    use HasFactory;

    protected $fillable = [
        'student_id',
        'competition_name',
        'level',
        'event_date',
        'location',
        'organizer',
        'category',
        'weight_class',
        'medal',
        'certificate_file',
        'notes',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'event_date' => 'date',
            'level' => CompetitionLevel::class,
            'medal' => MedalType::class,
            'is_published' => 'boolean',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function getLevelLabelAttribute(): string
    {
        return $this->level instanceof CompetitionLevel
            ? $this->level->label()
            : (CompetitionLevel::tryFrom((string) $this->level)?->label() ?? (string) $this->level);
    }

    public function getMedalLabelAttribute(): string
    {
        return $this->medal instanceof MedalType
            ? $this->medal->label()
            : (MedalType::tryFrom((string) $this->medal)?->label() ?? (string) $this->medal);
    }
}
