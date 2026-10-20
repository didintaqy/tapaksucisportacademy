<?php

namespace App\Filament\Resources\BeltPromotions\Tables;

use App\Enums\BeltRank;
use App\Enums\PromotionResult;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BeltPromotionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('exam_date')
                    ->label('Tanggal UKT')
                    ->date('d/m/Y')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('student.full_name')
                    ->label('Siswa')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn ($state, $record) => "{$record->student->full_name} ({$record->student->student_code})")
                    ->description(fn ($record) => $record->student->branch),

                TextColumn::make('current_belt')
                    ->label('Sabuk Saat Ini')
                    ->formatStateUsing(fn ($state) => BeltRank::tryFrom($state)?->label() ?? $state)
                    ->badge()
                    ->color(fn ($state) => BeltRank::tryFrom($state)?->color() ?? 'gray'),

                TextColumn::make('target_belt')
                    ->label('Sabuk Target')
                    ->formatStateUsing(fn ($state) => BeltRank::tryFrom($state)?->label() ?? $state)
                    ->badge()
                    ->color(fn ($state) => BeltRank::tryFrom($state)?->color() ?? 'gray'),

                BadgeColumn::make('result')
                    ->label('Hasil')
                    ->colors([
                        'success' => 'lulus',
                        'danger' => 'tidak_lulus',
                        'warning' => 'ditunda',
                    ])
                    ->formatStateUsing(fn ($state) => PromotionResult::tryFrom($state)?->label() ?? $state)
                    ->icons([
                        'lulus' => 'heroicon-o-check-circle',
                        'tidak_lulus' => 'heroicon-o-x-circle',
                        'ditunda' => 'heroicon-o-clock',
                    ]),

                TextColumn::make('examiner.name')
                    ->label('Penguji')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('certificate_number')
                    ->label('No. Sertifikat')
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('certificate_issued_at')
                    ->label('Tgl Terbit Sertifikat')
                    ->date('d/m/Y')
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('result')
                    ->label('Hasil')
                    ->options([
                        'lulus' => 'Lulus',
                        'tidak_lulus' => 'Tidak Lulus',
                        'ditunda' => 'Ditunda',
                    ]),

                SelectFilter::make('target_belt')
                    ->label('Sabuk Target')
                    ->options([
                        'kuning' => 'Kuning',
                        'hijau' => 'Hijau',
                        'biru' => 'Biru',
                        'merah' => 'Merah',
                        'coklat' => 'Coklat',
                        'hitam_1' => 'Hitam 1 Dan',
                        'hitam_2' => 'Hitam 2 Dan',
                        'hitam_3' => 'Hitam 3 Dan',
                        'hitam_4' => 'Hitam 4 Dan',
                        'hitam_5' => 'Hitam 5 Dan',
                    ]),

                Filter::make('exam_date')
                    ->form([
                        DatePicker::make('from')->label('Dari Tanggal'),
                        DatePicker::make('until')->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): void {
                        $query->when($data['from'], fn ($q, $date) => $q->whereDate('exam_date', '>=', $date));
                        $query->when($data['until'], fn ($q, $date) => $q->whereDate('exam_date', '<=', $date));
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
                Action::make('view_certificate')
                    ->label('Lihat Sertifikat')
                    ->icon('heroicon-o-document-text')
                    ->color('success')
                    ->visible(fn ($record) => $record->result === PromotionResult::Lulus && $record->certificate_number)
                    ->url(fn ($record) => route('belt-promotions.certificate', $record), shouldOpenInNewTab: true),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('exam_date', 'desc')
            ->groups([
                'result',
            ]);
    }
}
