<?php

namespace App\Filament\Resources\CoachPayrolls\Schemas;

use App\Enums\PayrollStatus;
use App\Models\CoachRate;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CoachPayrollForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Honor Pelatih')
                    ->columns(2)
                    ->schema([
                        Select::make('coach_id')
                            ->label('Pelatih')
                            ->relationship('coach', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        TextInput::make('period')
                            ->label('Periode')
                            ->placeholder('2026-11')
                            ->helperText('Format YYYY-MM. Satu pelatih hanya punya satu rekap per periode.')
                            ->rule('regex:/^\d{4}-(0[1-9]|1[0-2])$/')
                            ->required(),

                        TextInput::make('sessions_count')
                            ->label('Jumlah Sesi')
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->required(),

                        Select::make('rate_type')
                            ->label('Jenis Tarif')
                            ->options(CoachRate::TYPES)
                            ->default('per_session')
                            ->required(),

                        TextInput::make('rate_amount')
                            ->label('Nominal Tarif')
                            ->numeric()
                            ->minValue(0)
                            ->prefix('Rp')
                            ->default(0),

                        TextInput::make('bonus')
                            ->label('Bonus')
                            ->numeric()
                            ->minValue(0)
                            ->prefix('Rp')
                            ->default(0),

                        TextInput::make('deduction')
                            ->label('Potongan')
                            ->numeric()
                            ->minValue(0)
                            ->prefix('Rp')
                            ->default(0),

                        Select::make('status')
                            ->label('Status')
                            ->options(collect(PayrollStatus::cases())
                                ->mapWithKeys(fn (PayrollStatus $status): array => [$status->value => $status->getLabel()])
                                ->all())
                            ->default(PayrollStatus::Draft->value)
                            ->required(),

                        DateTimePicker::make('paid_at')
                            ->label('Dibayar Pada'),

                        Textarea::make('notes')
                            ->label('Catatan')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
