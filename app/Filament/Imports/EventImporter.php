<?php

namespace App\Filament\Imports;

use App\Models\Event;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;

class EventImporter extends BaseImporter
{
    protected static ?string $model = Event::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('title')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('description'),
            ImportColumn::make('starts_at')
                ->requiredMapping()
                ->rules(['required', 'datetime']),
            ImportColumn::make('ends_at')
                ->rules(['datetime']),
            ImportColumn::make('type')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('is_public')
                ->requiredMapping()
                ->boolean()
                ->rules(['required', 'boolean']),
            ImportColumn::make('created_by')
                ->numeric()
                ->rules(['integer']),
        ];
    }

    protected static function uniqueColumns(): ?array
    {
        return [
            'title',
            'starts_at',
        ];
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your event import has completed and '.Number::format($import->successful_rows).' '.str('row')->plural($import->successful_rows).' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' '.Number::format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to import.';
        }

        return $body;
    }
}
