<?php

namespace App\Filament\Imports;

use App\Models\Faq;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;

class FaqImporter extends BaseImporter
{
    protected static ?string $model = Faq::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('category')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('question')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('answer')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('sort_order')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
            ImportColumn::make('is_published')
                ->requiredMapping()
                ->boolean()
                ->rules(['required', 'boolean']),
        ];
    }

    protected static function uniqueColumns(): ?array
    {
        return [
            'question',
        ];
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your faq import has completed and '.Number::format($import->successful_rows).' '.str('row')->plural($import->successful_rows).' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' '.Number::format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to import.';
        }

        return $body;
    }
}
