<?php

namespace App\Models;

use App\Enums\StockMovementType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    protected $fillable = [
        'item_id',
        'type',
        'quantity',
        'unit_price',
        'reference',
        'notes',
        'moved_at',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'type' => StockMovementType::class,
            'quantity' => 'integer',
            'unit_price' => 'decimal:2',
            'moved_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (StockMovement $movement): void {
            $movement->moved_at ??= now();
            $movement->created_by ??= auth()->id();
        });

        static::created(function (StockMovement $movement): void {
            $item = $movement->item;

            if ($item === null) {
                return;
            }

            $newStock = match ($movement->type) {
                StockMovementType::In => $item->stock + $movement->quantity,
                StockMovementType::Out => max(0, $item->stock - $movement->quantity),
                StockMovementType::Adjustment => $movement->quantity,
            };

            $item->forceFill(['stock' => $newStock])->saveQuietly();
        });
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
