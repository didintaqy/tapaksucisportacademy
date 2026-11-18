<?php

namespace App\Filament\Resources\CoachRates;

use App\Filament\Resources\CoachRates\Pages\CreateCoachRate;
use App\Filament\Resources\CoachRates\Pages\EditCoachRate;
use App\Filament\Resources\CoachRates\Pages\ListCoachRates;
use App\Filament\Resources\CoachRates\Schemas\CoachRateForm;
use App\Filament\Resources\CoachRates\Tables\CoachRatesTable;
use App\Models\CoachRate;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class CoachRateResource extends Resource
{
    protected static ?string $model = CoachRate::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static UnitEnum|string|null $navigationGroup = 'Keuangan';

    protected static ?int $navigationSort = 3;

    public static function getNavigationLabel(): string
    {
        return 'Tarif Pelatih';
    }

    public static function getModelLabel(): string
    {
        return 'Tarif Pelatih';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Tarif Pelatih';
    }

    public static function form(Schema $schema): Schema
    {
        return CoachRateForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CoachRatesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCoachRates::route('/'),
            'create' => CreateCoachRate::route('/create'),
            'edit' => EditCoachRate::route('/{record}/edit'),
        ];
    }
}
