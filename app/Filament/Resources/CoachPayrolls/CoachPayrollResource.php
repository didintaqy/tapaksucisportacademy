<?php

namespace App\Filament\Resources\CoachPayrolls;

use App\Filament\Resources\CoachPayrolls\Pages\CreateCoachPayroll;
use App\Filament\Resources\CoachPayrolls\Pages\EditCoachPayroll;
use App\Filament\Resources\CoachPayrolls\Pages\ListCoachPayrolls;
use App\Filament\Resources\CoachPayrolls\Schemas\CoachPayrollForm;
use App\Filament\Resources\CoachPayrolls\Tables\CoachPayrollsTable;
use App\Models\CoachPayroll;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class CoachPayrollResource extends Resource
{
    protected static ?string $model = CoachPayroll::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static UnitEnum|string|null $navigationGroup = 'Keuangan';

    protected static ?int $navigationSort = 4;

    public static function getNavigationLabel(): string
    {
        return 'Honor Pelatih';
    }

    public static function getModelLabel(): string
    {
        return 'Honor Pelatih';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Honor Pelatih';
    }

    public static function form(Schema $schema): Schema
    {
        return CoachPayrollForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CoachPayrollsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCoachPayrolls::route('/'),
            'create' => CreateCoachPayroll::route('/create'),
            'edit' => EditCoachPayroll::route('/{record}/edit'),
        ];
    }
}
