<?php

namespace App\Filament\Resources\Testimonials\Tables;

use App\Enums\TestimonialStatus;
use App\Models\Testimonial;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TestimonialsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('relation')
                    ->label('Keterangan')
                    ->placeholder('-')
                    ->toggleable(),

                TextColumn::make('rating')
                    ->label('Rating')
                    ->suffix('/5')
                    ->sortable(),

                TextColumn::make('content')
                    ->label('Testimoni')
                    ->limit(60)
                    ->wrap(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->sortable(),

                IconColumn::make('is_featured')
                    ->label('Unggulan')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->label('Dikirim')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(TestimonialStatus::class),
            ])
            ->recordActions([
                Action::make('approve')
                    ->label('Setujui')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Testimonial $record): bool => $record->status !== TestimonialStatus::Approved)
                    ->requiresConfirmation()
                    ->action(fn (Testimonial $record) => $record->approve())
                    ->successNotificationTitle('Testimoni disetujui'),

                Action::make('reject')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (Testimonial $record): bool => $record->status !== TestimonialStatus::Rejected)
                    ->requiresConfirmation()
                    ->action(fn (Testimonial $record) => $record->reject())
                    ->successNotificationTitle('Testimoni ditolak'),

                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
