<?php

namespace App\Filament\Resources\Programs\Tables;

use App\Enums\ProgramLevel;
use App\Models\Program;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class ProgramsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->columns([
                ImageColumn::make('cover_image')
                    ->label('Sampul')
                    ->getStateUsing(fn (Program $record): string => $record->cover_url)
                    ->imageSize(48)
                    ->square(),

                TextColumn::make('name')
                    ->label('Nama Program')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                TextColumn::make('level')
                    ->label('Tingkat')
                    ->badge()
                    ->sortable(),

                TextColumn::make('age_range_label')
                    ->label('Usia')
                    ->toggleable(),

                TextColumn::make('branches')
                    ->label('Cabang')
                    ->badge()
                    ->separator(','),

                TextColumn::make('monthly_fee_label')
                    ->label('Iuran')
                    ->sortable(query: fn ($query, string $direction) => $query->orderBy('monthly_fee', $direction)),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),

                IconColumn::make('is_featured')
                    ->label('Unggulan')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('sort_order')
                    ->label('Urutan')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('students_count')
                    ->label('Siswa')
                    ->counts('students')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Status Aktif'),

                TernaryFilter::make('is_featured')
                    ->label('Unggulan'),

                SelectFilter::make('level')
                    ->label('Tingkat')
                    ->options(ProgramLevel::class),

                SelectFilter::make('branch')
                    ->label('Cabang')
                    ->options(fn (): array => Program::branchOptions())
                    ->query(fn ($query, array $data) => filled($data['value'])
                        ? $query->whereJsonContains('branches', $data['value'])
                        : $query),

                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
