<?php

namespace App\Filament\Resources\Enrollments\Schemas;

use App\Enums\EnrollmentStatus;
use App\Models\Student;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EnrollmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Keanggotaan')
                    ->columns(2)
                    ->schema([
                        Select::make('student_id')
                            ->label('Siswa')
                            ->relationship('student', 'full_name')
                            ->getOptionLabelFromRecordUsing(
                                fn (Student $record): string => "{$record->full_name} ({$record->student_code})"
                            )
                            ->searchable(['full_name', 'student_code'])
                            ->preload()
                            ->required(),

                        Select::make('program_id')
                            ->label('Program')
                            ->relationship('program', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('branch_id')
                            ->label('Cabang')
                            ->relationship('branch', 'name')
                            ->searchable()
                            ->preload(),

                        Select::make('coach_id')
                            ->label('Pelatih')
                            ->relationship('coach', 'name')
                            ->searchable()
                            ->preload(),

                        Select::make('status')
                            ->label('Status')
                            ->options(collect(EnrollmentStatus::cases())
                                ->mapWithKeys(fn (EnrollmentStatus $status): array => [$status->value => $status->getLabel()])
                                ->all())
                            ->default(EnrollmentStatus::Active->value)
                            ->required(),

                        DatePicker::make('started_at')
                            ->label('Mulai')
                            ->default(now())
                            ->required(),

                        DatePicker::make('ended_at')
                            ->label('Selesai')
                            ->helperText('Kosongkan selama masih aktif.'),

                        Textarea::make('notes')
                            ->label('Catatan')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
