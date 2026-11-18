<?php

namespace App\Filament\Resources\CoachPayrolls\Tables;

use App\Models\CoachPayroll;
use App\Services\Payroll\CoachPayrollCalculator;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CoachPayrollsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('period', 'desc')
            ->columns([
                TextColumn::make('coach.name')
                    ->label('Pelatih')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('period')
                    ->label('Periode')
                    ->sortable(),

                TextColumn::make('sessions_count')
                    ->label('Sesi')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('rate_amount')
                    ->label('Tarif')
                    ->money('IDR'),

                TextColumn::make('total')
                    ->label('Total')
                    ->money('IDR')
                    ->weight('bold')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge(),

                TextColumn::make('paid_at')
                    ->label('Dibayar')
                    ->dateTime('d M Y')
                    ->placeholder('-')
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('period')
                    ->label('Periode')
                    ->options(fn (): array => CoachPayroll::query()
                        ->distinct()
                        ->orderByDesc('period')
                        ->pluck('period', 'period')
                        ->all()),

                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'draft' => 'Draf',
                        'approved' => 'Disetujui',
                        'paid' => 'Dibayar',
                    ]),

                SelectFilter::make('coach_id')
                    ->label('Pelatih')
                    ->relationship('coach', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                Action::make('calculate')
                    ->label('Hitung dari Absensi')
                    ->icon('heroicon-o-calculator')
                    ->requiresConfirmation()
                    ->modalDescription('Sesi dihitung ulang dari absensi. Payroll yang sudah disetujui/dibayar tidak diubah.')
                    ->action(function (CoachPayroll $record): void {
                        app(CoachPayrollCalculator::class)->draftFor($record->coach, $record->period);

                        Notification::make()
                            ->title('Perhitungan diperbarui')
                            ->success()
                            ->send();
                    }),

                Action::make('markPaid')
                    ->label('Tandai Dibayar')
                    ->icon('heroicon-o-check-circle')
                    ->visible(fn (CoachPayroll $record): bool => ! $record->isPaid())
                    ->requiresConfirmation()
                    ->action(function (CoachPayroll $record): void {
                        $record->markAsPaid();

                        Notification::make()
                            ->title('Honor ditandai dibayar')
                            ->success()
                            ->send();
                    }),

                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
