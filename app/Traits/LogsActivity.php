<?php

namespace App\Traits;

use App\Models\ActivityLog;

/**
 * Mencatat created/updated/deleted ke tabel `activity_log`.
 * Kegagalan pencatatan tidak boleh menggagalkan operasi utama.
 */
trait LogsActivity
{
    public static function bootLogsActivity(): void
    {
        static::created(function ($model): void {
            static::writeActivity($model, 'created', [], $model->getAttributes());
        });

        static::updated(function ($model): void {
            $changes = $model->getChanges();
            unset($changes['updated_at']);

            if ($changes === []) {
                return;
            }

            $old = [];

            foreach (array_keys($changes) as $key) {
                $old[$key] = $model->getOriginal($key);
            }

            static::writeActivity($model, 'updated', $old, $changes);
        });

        static::deleted(function ($model): void {
            static::writeActivity($model, 'deleted', $model->getOriginal(), []);
        });

        // `restored` hanya tersedia pada model yang memakai SoftDeletes.
        if (method_exists(static::class, 'restored')) {
            static::restored(function ($model): void {
                static::writeActivity($model, 'restored', [], $model->getAttributes());
            });
        }
    }

    /**
     * @param  array<string, mixed>  $old
     * @param  array<string, mixed>  $attributes
     */
    protected static function writeActivity(object $model, string $event, array $old, array $attributes): void
    {
        try {
            ActivityLog::create([
                'subject_type' => $model::class,
                'subject_id' => $model->getKey(),
                'causer_id' => auth()->id(),
                'event' => $event,
                'properties' => array_filter([
                    'old' => $old ?: null,
                    'attributes' => $attributes ?: null,
                ], fn ($value): bool => $value !== null),
            ]);
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
