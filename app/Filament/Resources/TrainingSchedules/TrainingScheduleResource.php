<?php

namespace App\Filament\Resources\TrainingSchedules;

use App\Filament\Resources\TrainingSchedules\Pages\CreateTrainingSchedule;
use App\Filament\Resources\TrainingSchedules\Pages\EditTrainingSchedule;
use App\Filament\Resources\TrainingSchedules\Pages\ListTrainingSchedules;
use App\Filament\Resources\TrainingSchedules\Schemas\TrainingScheduleForm;
use App\Filament\Resources\TrainingSchedules\Tables\TrainingSchedulesTable;
use App\Models\TrainingSchedule;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class TrainingScheduleResource extends Resource
{
    protected static string|UnitEnum|null $navigationGroup = 'Manajemen Web';

    protected static ?string $navigationLabel = 'Jadwal Latihan';

    protected static ?string $modelLabel = 'Jadwal Latihan';

    protected static ?string $pluralModelLabel = 'Jadwal Latihan';

    protected static ?int $navigationSort = 6;

    protected static ?string $model = TrainingSchedule::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::CalendarDays;

    public static function form(Schema $schema): Schema
    {
        return TrainingScheduleForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TrainingSchedulesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTrainingSchedules::route('/'),
            'create' => CreateTrainingSchedule::route('/create'),
            'edit' => EditTrainingSchedule::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('coach');
    }
}
