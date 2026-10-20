<?php

namespace App\Filament\Resources\Events\Tables;

use Carbon\Carbon;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class EventsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->sortable()
                    ->limit(40),

                TextColumn::make('starts_at')
                    ->label('Mulai')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('ends_at')
                    ->label('Selesai')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->placeholder('-'),

                BadgeColumn::make('type')
                    ->label('Jenis')
                    ->colors([
                        'primary' => 'latihan',
                        'danger' => 'kejuaraan',
                        'warning' => 'ukt',
                        'info' => 'pengumuman',
                        'secondary' => 'libur',
                    ])
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'latihan' => 'Latihan',
                        'kejuaraan' => 'Kejuaraan',
                        'ukt' => 'UKT',
                        'pengumuman' => 'Pengumuman',
                        'libur' => 'Libur',
                        default => $state,
                    })
                    ->icons([
                        'latihan' => 'heroicon-o-cpu-chip',
                        'kejuaraan' => 'heroicon-o-trophy',
                        'ukt' => 'heroicon-o-badge-check',
                        'pengumuman' => 'heroicon-o-megaphone',
                        'libur' => 'heroicon-o-sun',
                    ]),

                IconColumn::make('is_public')
                    ->label('Publik')
                    ->boolean()
                    ->trueIcon('heroicon-o-eye')
                    ->falseIcon('heroicon-o-eye-slash')
                    ->trueColor('success')
                    ->falseColor('gray'),

                TextColumn::make('creator.name')
                    ->label('Dibuat Oleh')
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Jenis')
                    ->options([
                        'latihan' => 'Latihan',
                        'kejuaraan' => 'Kejuaraan',
                        'ukt' => 'UKT',
                        'pengumuman' => 'Pengumuman',
                        'libur' => 'Libur',
                    ]),

                SelectFilter::make('is_public')
                    ->label('Visibilitas')
                    ->options([
                        '1' => 'Publik',
                        '0' => 'Internal',
                    ]),

                Filter::make('starts_at')
                    ->form([
                        DatePicker::make('from')->label('Dari Tanggal'),
                        DatePicker::make('until')->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): void {
                        $query->when($data['from'], fn ($q, $date) => $q->whereDate('starts_at', '>=', $date));
                        $query->when($data['until'], fn ($q, $date) => $q->whereDate('starts_at', '<=', $date));
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['from']) {
                            $indicators['from'] = 'Dari '.Carbon::parse($data['from'])->format('d/m/Y');
                        }
                        if ($data['until']) {
                            $indicators['until'] = 'Sampai '.Carbon::parse($data['until'])->format('d/m/Y');
                        }

                        return $indicators;
                    }),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('starts_at', 'desc')
            ->groups([
                'type',
            ]);
    }
}
