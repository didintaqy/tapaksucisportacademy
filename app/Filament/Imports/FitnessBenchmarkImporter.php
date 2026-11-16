<?php

namespace App\Filament\Imports;

use App\Models\FitnessBenchmark;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;

class FitnessBenchmarkImporter extends BaseImporter
{
    protected static ?string $model = FitnessBenchmark::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('fitness_test_item_id')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
            ImportColumn::make('gender')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('age_min')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
            ImportColumn::make('age_max')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
            ImportColumn::make('target_value')
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
        return ['fitness_test_item_id', 'gender', 'age_min', 'age_max'];
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your fitness benchmark import has completed and '.Number::format($import->successful_rows).' '.str('row')->plural($import->successful_rows).' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' '.Number::format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to import.';
        }

        return $body;
    }
}
