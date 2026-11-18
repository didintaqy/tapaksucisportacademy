<?php

namespace App\Filament\Resources\EventParticipants\Schemas;

use App\Models\EventParticipant;
use App\Models\Student;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EventParticipantForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Peserta Event')
                    ->columns(2)
                    ->schema([
                        Select::make('event_id')
                            ->label('Event')
                            ->relationship('event', 'title')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('student_id')
                            ->label('Siswa')
                            ->relationship('student', 'full_name')
                            ->getOptionLabelFromRecordUsing(
                                fn (Student $record): string => "{$record->full_name} ({$record->student_code})"
                            )
                            ->searchable(['full_name', 'student_code'])
                            ->preload()
                            ->required(),

                        Select::make('status')
                            ->label('Status')
                            ->options(EventParticipant::STATUSES)
                            ->default('registered')
                            ->required(),

                        TextInput::make('fee_amount')
                            ->label('Biaya')
                            ->numeric()
                            ->minValue(0)
                            ->prefix('Rp'),

                        DateTimePicker::make('registered_at')
                            ->label('Waktu Daftar')
                            ->default(now()),

                        TextInput::make('result_note')
                            ->label('Catatan Hasil')
                            ->maxLength(255)
                            ->placeholder('Contoh: Juara 2 kategori tanding'),
                    ]),
            ]);
    }
}
