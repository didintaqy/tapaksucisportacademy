<?php

namespace App\Filament\Imports;

use App\Models\Attendance;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;

class AttendanceImporter extends BaseImporter
{
    protected static ?string $model = Attendance::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('student')
                ->requiredMapping()
                ->relationship()
                ->rules(['required']),
            ImportColumn::make('trainingSchedule')
                ->requiredMapping()
                ->relationship()
                ->rules(['required']),
            ImportColumn::make('date')
                ->requiredMapping()
                ->rules(['required', 'date']),
            ImportColumn::make('status')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('notes'),
            ImportColumn::make('recorded_by')
                ->requiredMapping()
                ->numeric()
                ->rules(['required', 'integer']),
        ];
    }

    protected static function uniqueColumns(): ?array
    {
        return [
            'student_id',
            'training_schedule_id',
            'date',
        ];
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your attendance import has completed and '.Number::format($import->successful_rows).' '.str('row')->plural($import->successful_rows).' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' '.Number::format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to import.';
        }

        return $body;
    }
}
