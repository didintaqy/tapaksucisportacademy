<?php

namespace App\Filament\Resources\ContactMessages\Tables;

use App\Models\ContactMessage;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class ContactMessagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('phone')
                    ->label('Telepon')
                    ->placeholder('-')
                    ->toggleable(),

                TextColumn::make('message')
                    ->label('Pesan')
                    ->limit(50)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }
                        return $state;
                    })
                    ->wrap(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'unread' => 'danger',
                        'read' => 'warning',
                        'replied' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'unread' => 'Belum Dibaca',
                        'read' => 'Sudah Dibaca',
                        'replied' => 'Sudah Dibalas',
                        default => $state,
                    })
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Diterima')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'unread' => 'Belum Dibaca',
                        'read' => 'Sudah Dibaca',
                        'replied' => 'Sudah Dibalas',
                    ])
                    ->multiple(),
            ])
            ->recordActions([
                ViewAction::make(),
                Action::make('mark_read')
                    ->label('Tandai Dibaca')
                    ->icon('heroicon-o-eye')
                    ->color('warning')
                    ->visible(fn (ContactMessage $record): bool => $record->status === 'unread')
                    ->action(function (ContactMessage $record): void {
                        $record->markAsRead();
                    })
                    ->requiresConfirmation()
                    ->successNotificationTitle('Pesan ditandai sudah dibaca'),

                Action::make('mark_replied')
                    ->label('Tandai Dibalas')
                    ->icon('heroicon-o-arrow-path')
                    ->color('success')
                    ->visible(fn (ContactMessage $record): bool => $record->status !== 'replied')
                    ->action(function (ContactMessage $record): void {
                        $record->markAsReplied();
                    })
                    ->requiresConfirmation()
                    ->successNotificationTitle('Pesan ditandai sudah dibalas'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    Action::make('mark_read_bulk')
                        ->label('Tandai Dibaca')
                        ->icon('heroicon-o-eye')
                        ->color('warning')
                        ->action(function (Collection $records): void {
                            $records->each(function (ContactMessage $record): void {
                                $record->markAsRead();
                            });
                        })
                        ->deselectRecordsAfterCompletion()
                        ->successNotificationTitle('Pesan terpilih ditandai sudah dibaca'),

                    Action::make('mark_replied_bulk')
                        ->label('Tandai Dibalas')
                        ->icon('heroicon-o-arrow-path')
                        ->color('success')
                        ->action(function (Collection $records): void {
                            $records->each(function (ContactMessage $record): void {
                                $record->markAsReplied();
                            });
                        })
                        ->deselectRecordsAfterCompletion()
                        ->successNotificationTitle('Pesan terpilih ditandai sudah dibalas'),

                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}