<?php

namespace App\Filament\Resources\Newsletters\Tables;

use App\Models\Newsletter;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class NewslettersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('subject')
                    ->label('Subjek')
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    ->limit(50),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => 'Draf',
                        'queued' => 'Antrean',
                        'sent' => 'Terkirim',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'warning',
                        'queued' => 'info',
                        'sent' => 'success',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('recipients_count')
                    ->label('Penerima')
                    ->numeric()
                    ->sortable()
                    ->placeholder('—'),

                TextColumn::make('sent_at')
                    ->label('Dikirim')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->placeholder('Belum dikirim'),

                TextColumn::make('creator.name')
                    ->label('Pengirim')
                    ->sortable()
                    ->placeholder('—'),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'draft' => 'Draf',
                        'queued' => 'Antrean',
                        'sent' => 'Terkirim',
                    ]),

                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('send')
                    ->label('Kirim')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('success')
                    ->authorize('send')
                    ->visible(fn ($record) => $record->isDraft())
                    ->requiresConfirmation()
                    ->modalHeading('Kirim Newsletter')
                    ->modalDescription('Apakah Anda yakin ingin mengirim newsletter ini ke semua pelanggan? Tindakan ini tidak dapat dibatalkan.')
                    ->action(function (Newsletter $record): void {
                        $record->send(auth()->user());
                        $record->refresh();

                        $queued = $record->isQueued();

                        Notification::make()
                            ->title($queued ? 'Newsletter masuk antrean' : 'Newsletter terkirim')
                            ->body($queued
                                ? "Newsletter \"{$record->subject}\" sedang diproses oleh queue worker."
                                : "Newsletter \"{$record->subject}\" telah dikirim ke {$record->recipients_count} penerima.")
                            ->success()
                            ->send();
                    }),
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
