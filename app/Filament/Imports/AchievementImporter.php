<?php

namespace App\Filament\Imports;

use App\Models\Achievement;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;

class AchievementImporter extends BaseImporter
{
    protected static ?string $model = Achievement::class;

    public static function getColumns(): array
    {
        return [
            //
        ];
    }

    protected static function uniqueColumns(): ?array
    {
        return [
            'competition_name',
            'event_date',
        ];
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your achievement import has completed and '.Number::format($import->successful_rows).' '.str('row')->plural($import->successful_rows).' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' '.Number::format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to import.';
        }

        return $body;
    }
}
