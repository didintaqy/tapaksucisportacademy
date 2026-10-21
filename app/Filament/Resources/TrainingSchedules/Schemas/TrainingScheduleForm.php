<?php

namespace App\Filament\Resources\TrainingSchedules\Schemas;

use App\Models\TrainingSchedule;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class TrainingScheduleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('program_id')
                    ->label('Program Latihan')
                    ->relationship('program', 'name')
                    ->searchable()
                    ->preload()
                    ->placeholder('Tanpa program'),

                TextInput::make('class_name')
                    ->label('Nama Kelas')
                    ->required()
                    ->maxLength(255),

                TextInput::make('branch')
                    ->label('Cabang')
                    ->required()
                    ->maxLength(255),

                Select::make('day_of_week')
                    ->label('Hari')
                    ->options(TrainingSchedule::DAYS)
                    ->required()
                    ->native(false),

                TimePicker::make('start_time')
                    ->label('Jam Mulai')
                    ->required()
                    ->seconds(false),

                TimePicker::make('end_time')
                    ->label('Jam Selesai')
                    ->required()
                    ->seconds(false)
                    ->after('start_time'),

                TextInput::make('location')
                    ->label('Lokasi')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),

                Select::make('coach_id')
                    ->label('Pelatih')
                    ->options(fn () => User::where('role', 'pelatih')
                        ->orderBy('name')
                        ->pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->placeholder('Pilih pelatih'),

                TextInput::make('sort_order')
                    ->label('Urutan')
                    ->numeric()
                    ->default(0)
                    ->minValue(0),

                Toggle::make('is_active')
                    ->label('Tampilkan di website')
                    ->default(true),
            ]);
    }
}
