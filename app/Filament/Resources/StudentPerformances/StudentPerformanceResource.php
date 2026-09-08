<?php

namespace App\Filament\Resources\StudentPerformances;

use App\Filament\Resources\StudentPerformances\Pages\CreateStudentPerformance;
use App\Filament\Resources\StudentPerformances\Pages\EditStudentPerformance;
use App\Filament\Resources\StudentPerformances\Pages\ListStudentPerformances;
use App\Filament\Resources\StudentPerformances\Pages\ViewStudentPerformance;
use App\Filament\Resources\StudentPerformances\Schemas\StudentPerformanceForm;
use App\Filament\Resources\StudentPerformances\Schemas\StudentPerformanceInfolist;
use App\Filament\Resources\StudentPerformances\Tables\StudentPerformancesTable;
use App\Models\StudentPerformance;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;


class StudentPerformanceResource extends Resource
{
    protected static string | UnitEnum | null $navigationGroup = 'Master Data';

    protected static ?string $navigationLabel = 'Performa Siswa';

    protected static ?int $navigationSort = 2;

    protected static ?string $model = StudentPerformance::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::PresentationChartLine;

    public static function form(Schema $schema): Schema
    {
        return StudentPerformanceForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return StudentPerformanceInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StudentPerformancesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStudentPerformances::route('/'),
            'create' => CreateStudentPerformance::route('/create'),
            'view' => ViewStudentPerformance::route('/{record}'),
            'edit' => EditStudentPerformance::route('/{record}/edit'),
        ];
    }
}
