<?php

namespace App\Models;

use App\Enums\PaymentPeriodType;
use Database\Factories\PaymentTypeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class PaymentType extends Model
{
    /** @use HasFactory<PaymentTypeFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'default_amount',
        'period_type',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'default_amount' => 'decimal:2',
            'period_type' => PaymentPeriodType::class,
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (PaymentType $type): void {
            if (blank($type->slug)) {
                $type->slug = Str::slug($type->name);
            }
        });
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function requiresPeriod(): bool
    {
        return $this->period_type?->requiresPeriod() ?? false;
    }
}
