<?php

namespace App\Filament\Resources\BeltPromotions;

use App\Filament\Resources\BeltPromotions\Pages\CreateBeltPromotion;
use App\Filament\Resources\BeltPromotions\Pages\EditBeltPromotion;
use App\Filament\Resources\BeltPromotions\Pages\ListBeltPromotions;
use App\Filament\Resources\BeltPromotions\Schemas\BeltPromotionForm;
use App\Filament\Resources\BeltPromotions\Tables\BeltPromotionsTable;
use App\Models\BeltPromotion;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class BeltPromotionResource extends Resource
{
    protected static ?string $model = BeltPromotion::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedIdentification;

    protected static string|UnitEnum|null $navigationGroup = 'Manajemen Akademi';

    protected static ?string $navigationLabel = 'Sabuk / UKT';

    protected static ?string $pluralModelLabel = 'Sabuk / UKT';

    protected static ?string $modelLabel = 'Sabuk / UKT';

    public static function form(Schema $schema): Schema
    {
        return BeltPromotionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BeltPromotionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBeltPromotions::route('/'),
            'create' => CreateBeltPromotion::route('/create'),
            'edit' => EditBeltPromotion::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['student', 'examiner']);
    }
}
