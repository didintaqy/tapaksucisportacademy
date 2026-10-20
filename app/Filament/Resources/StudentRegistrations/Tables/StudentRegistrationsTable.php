<?php

namespace App\Filament\Resources\StudentRegistrations\Tables;

use App\Enums\RegistrationStatus;
use App\Models\StudentRegistration;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class StudentRegistrationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('full_name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable()
                    ->description(fn (StudentRegistration $record): ?string => $record->nickname),

                TextColumn::make('phone')
                    ->label('Telepon')
                    ->searchable()
                    ->copyable(),

                TextColumn::make('branch')
                    ->label('Cabang')
                    ->placeholder('-')
                    ->toggleable(),

                TextColumn::make('parent_name')
                    ->label('Orang Tua')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Masuk')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(RegistrationStatus::class),

                SelectFilter::make('branch')
                    ->label('Cabang')
                    ->options(fn (): array => StudentRegistration::query()
                        ->whereNotNull('branch')
                        ->distinct()
                        ->orderBy('branch')
                        ->pluck('branch', 'branch')
                        ->all()),
            ])
            ->recordActions([
                Action::make('contacted')
                    ->label('Dihubungi')
                    ->icon('heroicon-o-phone')
                    ->color('info')
                    ->visible(fn (StudentRegistration $record): bool => $record->status === RegistrationStatus::New)
                    ->requiresConfirmation()
                    ->action(fn (StudentRegistration $record) => $record->markAs(RegistrationStatus::Contacted, auth()->user()))
                    ->successNotificationTitle('Ditandai sudah dihubungi'),

                Action::make('accept')
                    ->label('Terima')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (StudentRegistration $record): bool => ! in_array($record->status, [RegistrationStatus::Accepted, RegistrationStatus::Rejected], true))
                    ->requiresConfirmation()
                    ->action(fn (StudentRegistration $record) => $record->markAs(RegistrationStatus::Accepted, auth()->user()))
                    ->successNotificationTitle('Pendaftaran diterima'),

                Action::make('reject')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (StudentRegistration $record): bool => ! in_array($record->status, [RegistrationStatus::Accepted, RegistrationStatus::Rejected], true))
                    ->requiresConfirmation()
                    ->action(fn (StudentRegistration $record) => $record->markAs(RegistrationStatus::Rejected, auth()->user()))
                    ->successNotificationTitle('Pendaftaran ditolak'),

                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                CreateAction::make()->label('Tambah Pendaftaran'),
            ]);
    }
}
