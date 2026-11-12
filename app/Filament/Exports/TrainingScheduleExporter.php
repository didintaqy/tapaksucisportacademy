<?php

namespace App\Filament\Exports;

use App\Models\TrainingSchedule;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

class TrainingScheduleExporter extends Exporter
{
    protected static ?string $model = TrainingSchedule::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('class_name'),
            ExportColumn::make('branch'),
            ExportColumn::make('day_of_week'),
            ExportColumn::make('start_time'),
            ExportColumn::make('end_time'),
            ExportColumn::make('location'),
            ExportColumn::make('coach_name'),
            ExportColumn::make('is_active'),
            ExportColumn::make('sort_order'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
            ExportColumn::make('coach.name'),
            ExportColumn::make('program.name'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your training schedule export has completed and '.Number::format($export->successful_rows).' '.str('row')->plural($export->successful_rows).' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.Number::format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to export.';
        }

        return $body;
    }
}
