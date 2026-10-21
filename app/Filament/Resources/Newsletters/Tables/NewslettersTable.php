<?php

namespace App\Filament\Resources\Newsletters\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

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

                IconColumn::make('is_draft')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-pencil-square')
                    ->falseIcon('heroicon-o-check-circle')
                    ->trueColor('warning')
                    ->falseColor('success')
                    ->sortable()
                    ->getStateUsing(fn ($record) => $record->isDraft()),

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
                SelectFilter::make('is_draft')
                    ->label('Status')
                    ->options([
                        '1' => 'Draf',
                        '0' => 'Terkirim',
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query->where('status', $data['value'] === '1' ? 'draft' : 'sent')),

                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('send')
                    ->label('Kirim')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('success')
                    ->visible(fn ($record) => $record->isDraft())
                    ->requiresConfirmation()
                    ->modalHeading('Kirim Newsletter')
                    ->modalDescription('Apakah Anda yakin ingin mengirim newsletter ini ke semua pelanggan? Tindakan ini tidak dapat dibatalkan.')
                    ->action(function ($record): void {
                        $record->send();
                        Notification::make()
                            ->title('Newsletter terkirim')
                            ->body("Newsletter \"{$record->subject}\" telah dikirim ke {$record->recipients_count} penerima.")
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
