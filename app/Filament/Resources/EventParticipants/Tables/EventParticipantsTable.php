<?php

namespace App\Filament\Resources\EventParticipants\Tables;

use App\Models\EventParticipant;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class EventParticipantsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('registered_at', 'desc')
            ->columns([
                TextColumn::make('event.title')
                    ->label('Event')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                TextColumn::make('event.starts_at')
                    ->label('Tanggal Event')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('student.full_name')
                    ->label('Siswa')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record): ?string => $record->student?->student_code),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => EventParticipant::STATUSES[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        'registered' => 'gray',
                        'confirmed' => 'info',
                        'attended' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('fee_amount')
                    ->label('Biaya')
                    ->money('IDR')
                    ->placeholder('-'),

                TextColumn::make('registered_at')
                    ->label('Didaftarkan')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('result_note')
                    ->label('Hasil')
                    ->limit(30)
                    ->placeholder('-')
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('event_id')
                    ->label('Event')
                    ->relationship('event', 'title')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('status')
                    ->label('Status')
                    ->options(EventParticipant::STATUSES),
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
