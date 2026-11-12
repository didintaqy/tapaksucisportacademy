<?php

namespace App\Filament\Imports;

use App\Models\Coach;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;

class CoachImporter extends BaseImporter
{
    protected static ?string $model = Coach::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('title'),
            ImportColumn::make('specialization'),
            ImportColumn::make('bio'),
            ImportColumn::make('photo_path'),
            ImportColumn::make('experience_years')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('sort_order')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
            ImportColumn::make('is_active')
                ->requiredMapping()
                ->boolean()
                ->rules(['required', 'boolean']),
        ];
    }

    protected static function uniqueColumns(): ?array
    {
        return [
            'name',
        ];
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your coach import has completed and '.Number::format($import->successful_rows).' '.str('row')->plural($import->successful_rows).' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' '.Number::format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to import.';
        }

        return $body;
    }
}
