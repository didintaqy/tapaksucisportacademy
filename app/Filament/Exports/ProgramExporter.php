<?php

namespace App\Filament\Exports;

use App\Models\Program;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

class ProgramExporter extends Exporter
{
    protected static ?string $model = Program::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('name'),
            ExportColumn::make('slug'),
            ExportColumn::make('short_description'),
            ExportColumn::make('description'),
            ExportColumn::make('cover_image'),
            ExportColumn::make('age_min'),
            ExportColumn::make('age_max'),
            ExportColumn::make('level'),
            ExportColumn::make('session_duration_minutes'),
            ExportColumn::make('sessions_per_week'),
            ExportColumn::make('monthly_fee'),
            ExportColumn::make('branches'),
            ExportColumn::make('is_active'),
            ExportColumn::make('is_featured'),
            ExportColumn::make('sort_order'),
            ExportColumn::make('meta_title'),
            ExportColumn::make('meta_description'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
            ExportColumn::make('deleted_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your program export has completed and '.Number::format($export->successful_rows).' '.str('row')->plural($export->successful_rows).' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.Number::format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to export.';
        }

        return $body;
    }
}
