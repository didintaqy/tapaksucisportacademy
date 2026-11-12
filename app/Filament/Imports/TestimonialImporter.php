<?php

namespace App\Filament\Imports;

use App\Models\Testimonial;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;

class TestimonialImporter extends BaseImporter
{
    protected static ?string $model = Testimonial::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('relation'),
            ImportColumn::make('content')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('rating')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
            ImportColumn::make('status')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('is_featured')
                ->requiredMapping()
                ->boolean()
                ->rules(['required', 'boolean']),
            ImportColumn::make('published_at')
                ->rules(['datetime']),
            ImportColumn::make('created_by')
                ->numeric()
                ->rules(['integer']),
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
        $body = 'Your testimonial import has completed and '.Number::format($import->successful_rows).' '.str('row')->plural($import->successful_rows).' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' '.Number::format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to import.';
        }

        return $body;
    }
}
