<?php

namespace App\Filament\Imports;

use App\Models\Gallery;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;

class GalleryImporter extends BaseImporter
{
    protected static ?string $model = Gallery::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('title'),
            ImportColumn::make('category'),
            ImportColumn::make('description'),
            ImportColumn::make('image')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('taken_at')
                ->rules(['date']),
            ImportColumn::make('is_published')
                ->requiredMapping()
                ->boolean()
                ->rules(['required', 'boolean']),
            ImportColumn::make('sort_order')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
        ];
    }

    protected static function uniqueColumns(): ?array
    {
        return [
            'image',
        ];
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your gallery import has completed and '.Number::format($import->successful_rows).' '.str('row')->plural($import->successful_rows).' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' '.Number::format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to import.';
        }

        return $body;
    }
}
