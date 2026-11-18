<?php

namespace App\Traits;

use App\Models\Branch;

/**
 * Menjaga `branch_id` tetap sinkron dengan kolom string `branch` selama
 * masa transisi. Hapus trait ini setelah aplikasi sepenuhnya membaca
 * `branch_id` dan kolom string lama dihapus.
 */
trait SyncsBranch
{
    public static function bootSyncsBranch(): void
    {
        static::saving(function ($model): void {
            $name = trim((string) ($model->branch ?? ''));

            if ($name === '') {
                return;
            }

            if (blank($model->branch_id)) {
                $model->branch_id = Branch::resolveId($name);
            }
        });
    }
}
