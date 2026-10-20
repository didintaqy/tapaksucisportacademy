<?php

namespace App\Filament\Resources\Students\RelationManagers;

use App\Filament\Resources\StudentPerformances\StudentPerformanceResource;
use App\Models\StudentPerformance;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PerformancesRelationManager extends RelationManager
{
    protected static string $relationship = 'performances';

    protected static ?string $title = 'Riwayat Tes & Performa Fisik';

    protected static string|\BackedEnum|null $icon = 'heroicon-m-chart-bar';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('height')
                    ->label('Tinggi (m)')
                    ->numeric(),
                TextInput::make('weight')
                    ->label('Berat (kg)')
                    ->numeric(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('created_at')
            ->columns([
                TextColumn::make('created_at')
                    ->label('Tanggal Tes')
                    ->dateTime('d F Y')
                    ->sortable(),

                TextColumn::make('height')
                    ->label('TB (m)')
                    ->suffix(' m'),

                TextColumn::make('weight')
                    ->label('BB (kg)')
                    ->suffix(' kg'),

                TextColumn::make('kategori_bmi')
                    ->label('Status BMI')
                    ->badge()
                    ->state(function (StudentPerformance $record) {
                        if (! $record->height || ! $record->weight) {
                            return '-';
                        }
                        $bmi = $record->weight / ($record->height * $record->height);

                        return match (true) {
                            $bmi < 18.5 => 'KURUS',
                            $bmi < 23 => 'NORMAL',
                            $bmi < 25 => 'KELEBIHAN BB',
                            default => 'OBESITAS',
                        };
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'OBESITAS' => 'danger',
                        'NORMAL' => 'success',
                        default => 'warning',
                    }),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Tambah Tes Baru'),
            ])
            ->actions([
                Action::make('view_report')
                    ->label('Lihat Laporan Fisik')
                    ->icon('heroicon-m-eye')
                    ->color('info')
                    ->url(fn (StudentPerformance $record): string => StudentPerformanceResource::getUrl('view', ['record' => $record])),

                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
