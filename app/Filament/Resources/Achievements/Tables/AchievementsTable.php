<?php

namespace App\Filament\Resources\Achievements\Tables;

use App\Enums\CompetitionLevel;
use App\Enums\MedalType;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AchievementsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('event_date')
                    ->label('Tanggal')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('competition_name')
                    ->label('Nama Kejuaraan')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->location),

                TextColumn::make('student.full_name')
                    ->label('Atlet / Kontingen')
                    ->placeholder('Kontingen TSSA')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('level')
                    ->label('Tingkat')
                    ->badge()
                    ->formatStateUsing(fn ($state): string => $state instanceof CompetitionLevel
                        ? $state->label()
                        : (CompetitionLevel::tryFrom((string) $state)?->label() ?? (string) $state))
                    ->color(fn ($state): string => $state instanceof CompetitionLevel ? $state->color() : 'gray'),

                TextColumn::make('category')
                    ->label('Kategori / Kelas')
                    ->formatStateUsing(fn ($state, $record): string => trim(($record->category ?? '').($record->weight_class ? " • {$record->weight_class}" : '')) ?: '-')
                    ->searchable(),

                TextColumn::make('medal')
                    ->label('Medali')
                    ->badge()
                    ->formatStateUsing(fn ($state): string => $state instanceof MedalType
                        ? $state->label()
                        : (MedalType::tryFrom((string) $state)?->label() ?? (string) $state))
                    ->color(fn ($state): string => $state instanceof MedalType ? $state->color() : 'gray'),

                IconColumn::make('is_published')
                    ->label('Publik')
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('medal')
                    ->label('Medali')
                    ->options(MedalType::options()),

                SelectFilter::make('level')
                    ->label('Tingkat')
                    ->options(CompetitionLevel::options()),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('event_date', 'desc');
    }
}
