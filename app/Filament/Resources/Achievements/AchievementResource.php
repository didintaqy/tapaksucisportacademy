<?php

namespace App\Filament\Resources\Achievements;

use App\Filament\Resources\Achievements\Pages\CreateAchievement;
use App\Filament\Resources\Achievements\Pages\EditAchievement;
use App\Filament\Resources\Achievements\Pages\ListAchievements;
use App\Filament\Resources\Achievements\Schemas\AchievementForm;
use App\Filament\Resources\Achievements\Tables\AchievementsTable;
use App\Models\Achievement;
use App\Models\TrainingSchedule;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class AchievementResource extends Resource
{
    protected static ?string $model = Achievement::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTrophy;

    protected static string|UnitEnum|null $navigationGroup = 'Manajemen Akademi';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Prestasi Atlet';

    protected static ?string $pluralModelLabel = 'Prestasi Atlet';

    protected static ?string $modelLabel = 'Prestasi Atlet';

    protected static ?string $recordTitleAttribute = 'competition_name';

    public static function form(Schema $schema): Schema
    {
        return AchievementForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AchievementsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAchievements::route('/'),
            'create' => CreateAchievement::route('/create'),
            'edit' => EditAchievement::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with(['student']);

        if (auth()->user()?->isPelatih()) {
            $branches = TrainingSchedule::query()
                ->where('coach_id', auth()->id())
                ->whereNotNull('branch')
                ->pluck('branch')
                ->unique();

            $query->where(function (Builder $builder) use ($branches): void {
                $builder->whereNull('student_id')
                    ->orWhereHas(
                        'student',
                        fn (Builder $student): Builder => $student->whereIn('branch', $branches),
                    );
            });
        }

        return $query;
    }
}
