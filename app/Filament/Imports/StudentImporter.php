<?php

namespace App\Filament\Imports;

use App\Models\Student;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;

class StudentImporter extends BaseImporter
{
    protected static ?string $model = Student::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('full_name')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('nickname'),
            ImportColumn::make('birth_place'),
            ImportColumn::make('birth_date')
                ->rules(['date']),
            ImportColumn::make('nik'),
            ImportColumn::make('family_card_number'),
            ImportColumn::make('phone_number'),
            ImportColumn::make('gender'),
            ImportColumn::make('belt_level'),
            ImportColumn::make('address'),
            ImportColumn::make('education'),
            ImportColumn::make('photo'),
            ImportColumn::make('branch'),
            ImportColumn::make('student_code'),
            ImportColumn::make('email')
                ->rules(['email']),
            ImportColumn::make('program')
                ->relationship(),
        ];
    }

    protected static function uniqueColumns(): ?array
    {
        return ['nik'];
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your student import has completed and '.Number::format($import->successful_rows).' '.str('row')->plural($import->successful_rows).' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' '.Number::format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to import.';
        }

        return $body;
    }
}
