<?php

namespace App\Filament\Resources\FitnessBenchmarks;

use App\Filament\Resources\FitnessBenchmarks\Pages\CreateFitnessBenchmark;
use App\Filament\Resources\FitnessBenchmarks\Pages\EditFitnessBenchmark;
use App\Filament\Resources\FitnessBenchmarks\Pages\ListFitnessBenchmarks;
use App\Filament\Resources\FitnessBenchmarks\Schemas\FitnessBenchmarkForm;
use App\Filament\Resources\FitnessBenchmarks\Tables\FitnessBenchmarksTable;
use App\Models\FitnessBenchmark;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class FitnessBenchmarkResource extends Resource
{
    protected static ?string $model = FitnessBenchmark::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAdjustmentsHorizontal;

    protected static string|UnitEnum|null $navigationGroup = 'Master Data';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Benchmark Kebugaran';

    protected static ?string $pluralModelLabel = 'Benchmark Kebugaran';

    protected static ?string $modelLabel = 'Benchmark Kebugaran';

    public static function form(Schema $schema): Schema
    {
        return FitnessBenchmarkForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return FitnessBenchmarksTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFitnessBenchmarks::route('/'),
            'create' => CreateFitnessBenchmark::route('/create'),
            'edit' => EditFitnessBenchmark::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['testItem.component']);
    }
}
