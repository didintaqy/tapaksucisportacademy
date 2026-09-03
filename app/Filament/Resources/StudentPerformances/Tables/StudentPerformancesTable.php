<?php

namespace App\Filament\Resources\StudentPerformances\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StudentPerformancesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('student.id')
                    ->searchable(),
                TextColumn::make('resting_heart_rate')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('max_heart_rate')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('training_heart_rate')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('height')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('weight')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('sit_and_reach')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('push_up_1_min')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('sit_up_2_min')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('core_stability_level')
                    ->searchable(),
                TextColumn::make('triple_hop_right')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('triple_hop_left')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('illinois_agility')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('rast_average_power')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('rast_fatigue_index')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('beep_test_vo2max')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
