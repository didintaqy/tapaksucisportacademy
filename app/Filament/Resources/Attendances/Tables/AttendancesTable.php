<?php

namespace App\Filament\Resources\Attendances\Tables;

use App\Models\TrainingSchedule;
use Carbon\Carbon;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AttendancesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('date')
                    ->label('Tanggal')
                    ->date('d/m/Y')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('trainingSchedule.class_name')
                    ->label('Kelas')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn ($state, $record) => "{$record->trainingSchedule->class_name} - {$record->trainingSchedule->branch}"),

                TextColumn::make('trainingSchedule.day')
                    ->label('Hari')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'Senin' => 'primary',
                        'Selasa' => 'success',
                        'Rabu' => 'info',
                        'Kamis' => 'warning',
                        'Jumat' => 'danger',
                        'Sabtu' => 'secondary',
                        'Minggu' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => $state?->label() ?? $state),

                TextColumn::make('student.full_name')
                    ->label('Siswa')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn ($state, $record) => "{$record->student->full_name} ({$record->student->student_code})"),

                BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'success' => 'hadir',
                        'info' => 'izin',
                        'warning' => 'sakit',
                        'danger' => 'alpa',
                    ])
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'hadir' => 'Hadir',
                        'izin' => 'Izin',
                        'sakit' => 'Sakit',
                        'alpa' => 'Alpa',
                        default => $state,
                    })
                    ->icons([
                        'hadir' => 'heroicon-o-check-circle',
                        'izin' => 'heroicon-o-clock',
                        'sakit' => 'heroicon-o-face-frown',
                        'alpa' => 'heroicon-o-x-circle',
                    ]),

                TextColumn::make('notes')
                    ->label('Catatan')
                    ->limit(30)
                    ->placeholder('-')
                    ->toggleable(),

                TextColumn::make('recorder.name')
                    ->label('Dicatat Oleh')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('training_schedule_id')
                    ->label('Jadwal Latihan')
                    ->options(fn () => TrainingSchedule::all()->pluck('class_name', 'id')->mapWithKeys(fn ($name, $id) => [$id => $name])->toArray())
                    ->searchable()
                    ->preload(),

                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'hadir' => 'Hadir',
                        'izin' => 'Izin',
                        'sakit' => 'Sakit',
                        'alpa' => 'Alpa',
                    ]),

                Filter::make('date')
                    ->form([
                        DatePicker::make('from')->label('Dari Tanggal'),
                        DatePicker::make('until')->label('Sampai Tanggal'),
                    ])
                    ->query(function ($query, array $data): void {
                        $query->when($data['from'], fn ($q, $date) => $q->whereDate('date', '>=', $date));
                        $query->when($data['until'], fn ($q, $date) => $q->whereDate('date', '<=', $date));
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
            ->defaultSort('date', 'desc')
            ->groups([
                'date',
            ]);
    }
}
