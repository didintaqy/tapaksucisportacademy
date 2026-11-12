<?php

namespace App\Filament\Imports;

use App\Models\Program;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;

class ProgramImporter extends BaseImporter
{
    protected static ?string $model = Program::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('slug')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('short_description'),
            ImportColumn::make('description'),
            ImportColumn::make('cover_image'),
            ImportColumn::make('age_min')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('age_max')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('level')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('session_duration_minutes')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('sessions_per_week')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('monthly_fee')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('branches'),
            ImportColumn::make('is_active')
                ->requiredMapping()
                ->boolean()
                ->rules(['required', 'boolean']),
            ImportColumn::make('is_featured')
                ->requiredMapping()
                ->boolean()
                ->rules(['required', 'boolean']),
            ImportColumn::make('sort_order')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
            ImportColumn::make('meta_title'),
            ImportColumn::make('meta_description'),
        ];
    }

    protected static function uniqueColumns(): ?array
    {
        return [
            'slug',
        ];
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your program import has completed and '.Number::format($import->successful_rows).' '.str('row')->plural($import->successful_rows).' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' '.Number::format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to import.';
        }

        return $body;
    }
}
