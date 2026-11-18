<?php

namespace App\Filament\Resources\CoachRates\Schemas;

use App\Models\CoachRate;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CoachRateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Tarif Pelatih')
                    ->columns(2)
                    ->schema([
                        Select::make('coach_id')
                            ->label('Pelatih')
                            ->relationship('coach', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('rate_type')
                            ->label('Jenis Tarif')
                            ->options(CoachRate::TYPES)
                            ->default('per_session')
                            ->required(),

                        TextInput::make('amount')
                            ->label('Nominal')
                            ->numeric()
                            ->minValue(0)
                            ->prefix('Rp')
                            ->required(),

                        DatePicker::make('effective_from')
                            ->label('Berlaku Mulai')
                            ->default(now())
                            ->required(),

                        DatePicker::make('effective_to')
                            ->label('Berlaku Sampai')
                            ->helperText('Kosongkan bila tarif masih berlaku.'),

                        Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true),
                    ]),
            ]);
    }
}
