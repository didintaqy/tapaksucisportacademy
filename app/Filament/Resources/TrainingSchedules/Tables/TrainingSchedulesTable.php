<?php

namespace App\Filament\Resources\TrainingSchedules\Tables;

use App\Models\TrainingSchedule;
use App\Models\User;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class TrainingSchedulesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('day_of_week')
            ->columns([
                TextColumn::make('day_of_week')
                    ->label('Hari')
                    ->formatStateUsing(fn (int $state): string => TrainingSchedule::DAYS[$state] ?? '-')
                    ->sortable(),

                TextColumn::make('class_name')
                    ->label('Kelas')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('program.name')
                    ->label('Program')
                    ->placeholder('-')
                    ->toggleable(),

                TextColumn::make('branch')
                    ->label('Cabang')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('start_time')
                    ->label('Mulai')
                    ->time('H:i')
                    ->sortable(),

                TextColumn::make('end_time')
                    ->label('Selesai')
                    ->time('H:i'),

                TextColumn::make('location')
                    ->label('Lokasi')
                    ->limit(30)
                    ->toggleable(),

                TextColumn::make('coach.name')
                    ->label('Pelatih')
                    ->placeholder('-')
                    ->toggleable(),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),

                TextColumn::make('sort_order')
                    ->label('Urutan')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('day_of_week')
                    ->label('Hari')
                    ->options(TrainingSchedule::DAYS),

                SelectFilter::make('program_id')
                    ->label('Program')
                    ->relationship('program', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('branch')
                    ->label('Cabang')
                    ->options(fn (): array => TrainingSchedule::query()
                        ->distinct()
                        ->orderBy('branch')
                        ->pluck('branch', 'branch')
                        ->all()),

                SelectFilter::make('coach_id')
                    ->label('Pelatih')
                    ->options(fn () => User::where('role', 'pelatih')
                        ->orderBy('name')
                        ->pluck('name', 'id'))
                    ->searchable()
                    ->preload(),

                TernaryFilter::make('is_active')
                    ->label('Status')
                    ->trueLabel('Aktif')
                    ->falseLabel('Nonaktif'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
