<?php

namespace App\Filament\Imports;

use App\Models\TrainingSchedule;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;

class TrainingScheduleImporter extends BaseImporter
{
    protected static ?string $model = TrainingSchedule::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('class_name')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('branch')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('day_of_week')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
            ImportColumn::make('start_time')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('end_time')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('location')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('coach_name'),
            ImportColumn::make('is_active')
                ->requiredMapping()
                ->boolean()
                ->rules(['required', 'boolean']),
            ImportColumn::make('sort_order')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
            ImportColumn::make('coach')
                ->relationship(),
            ImportColumn::make('program')
                ->relationship(),
        ];
    }

    protected static function uniqueColumns(): ?array
    {
        return [
            'class_name',
            'day_of_week',
            'branch',
        ];
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your training schedule import has completed and '.Number::format($import->successful_rows).' '.str('row')->plural($import->successful_rows).' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' '.Number::format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to import.';
        }

        return $body;
    }
}
