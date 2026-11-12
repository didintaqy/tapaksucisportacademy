<?php

namespace App\Filament\Resources\FitnessBenchmarks\Tables;

use App\Models\FitnessTestItem;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class FitnessBenchmarksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('testItem.component.name')
                    ->label('Komponen')
                    ->badge()
                    ->sortable(),

                TextColumn::make('testItem.name')
                    ->label('Item Tes')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('gender')
                    ->label('Gender')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'L', 'Laki-laki' => 'Laki-laki',
                        'P', 'Perempuan' => 'Perempuan',
                        default => 'Semua',
                    }),

                TextColumn::make('age_range')
                    ->label('Kelompok Usia')
                    ->state(fn ($record): string => "{$record->age_min}–{$record->age_max} thn"),

                TextColumn::make('target_value')
                    ->label('Target Benchmark')
                    ->formatStateUsing(fn ($state, $record): string => rtrim(rtrim(number_format((float) $state, 2, ',', '.'), '0'), ',').' '.($record->testItem?->unit ?? ''))
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('fitness_test_item_id')
                    ->label('Item Tes')
                    ->options(fn () => FitnessTestItem::orderBy('sort_order')->pluck('name', 'id')),

                SelectFilter::make('gender')
                    ->label('Gender')
                    ->options([
                        'L' => 'Laki-laki',
                        'P' => 'Perempuan',
                        'Semua' => 'Semua',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('fitness_test_item_id');
    }
}
