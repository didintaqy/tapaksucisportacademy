<?php

namespace App\Filament\Exports;

use App\Models\Student;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

class StudentExporter extends Exporter
{
    protected static ?string $model = Student::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('full_name'),
            ExportColumn::make('nickname'),
            ExportColumn::make('birth_place'),
            ExportColumn::make('birth_date'),
            ExportColumn::make('nik'),
            ExportColumn::make('family_card_number'),
            ExportColumn::make('phone_number'),
            ExportColumn::make('gender'),
            ExportColumn::make('belt_level'),
            ExportColumn::make('address'),
            ExportColumn::make('education'),
            ExportColumn::make('photo'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
            ExportColumn::make('deleted_at'),
            ExportColumn::make('branch'),
            ExportColumn::make('student_code'),
            ExportColumn::make('email'),
            ExportColumn::make('program.name'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your student export has completed and '.Number::format($export->successful_rows).' '.str('row')->plural($export->successful_rows).' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.Number::format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to export.';
        }

        return $body;
    }
}
