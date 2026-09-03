<?php

namespace App\Filament\Resources\StudentPerformances\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class StudentPerformanceInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // A. BIODATA ATLET
                Section::make('A. BIODATA ATLET')
                    ->icon('heroicon-m-user')
                    ->schema([
                        Grid::make(4)->schema([
                            ImageEntry::make('student.photo')
                                ->label('Foto')
                                ->circular()
                                ->defaultImageUrl(url('/images/default-avatar.png')),
                            TextEntry::make('student.full_name')
                                ->label('Nama Lengkap')
                                ->weight('bold'),
                            TextEntry::make('student.birth_place')
                                ->label('Tempat Lahir'),
                            TextEntry::make('student.birth_date')
                                ->label('Tanggal Lahir')
                                ->date('d F Y'),
                            TextEntry::make('student.gender')
                                ->label('Jenis Kelamin')
                                ->badge()
                                ->formatStateUsing(fn($state) => $state === 'L' ? 'Laki-laki' : 'Perempuan'),
                            TextEntry::make('student.belt_level')
                                ->label('Tingkat Sabuk')
                                ->color('warning')
                                ->weight('bold'),
                            TextEntry::make('student.education')
                                ->label('Pendidikan'),
                            TextEntry::make('student.address')
                                ->label('Alamat')
                                ->columnSpan(2),
                        ]),
                    ]),

                // B & C. ANTROPOMETRI & SKRINING NADI
                Grid::make(2)->schema([
                    Section::make('B. TES ANTROPOMETRI')
                        ->icon('heroicon-m-scale')
                        ->schema([
                            Grid::make(2)->schema([
                                TextEntry::make('height')
                                    ->label('Tinggi Badan')
                                    ->suffix(' m')
                                    ->placeholder('-'),
                                TextEntry::make('weight')
                                    ->label('Berat Badan')
                                    ->suffix(' kg')
                                    ->placeholder('-'),
                                TextEntry::make('bmi')
                                    ->label('Body Mass Index (BMI)')
                                    ->state(function ($record) {
                                        if (!$record->height || !$record->weight) return '-';
                                        return round($record->weight / ($record->height * $record->height), 1);
                                    }),
                                TextEntry::make('bmi_status')
                                    ->label('Kategori')
                                    ->badge()
                                    ->state(function ($record) {
                                        if (!$record->height || !$record->weight) return '-';
                                        $bmi = $record->weight / ($record->height * $record->height);
                                        return match (true) {
                                            $bmi < 18.5 => 'Kurus',
                                            $bmi < 23 => 'Normal',
                                            $bmi < 25 => 'Kelebihan BB',
                                            default => 'OBESITAS',
                                        };
                                    })
                                    ->color(fn($state) => match ($state) {
                                        'OBESITAS' => 'danger',
                                        'Normal' => 'success',
                                        default => 'warning',
                                    }),
                            ]),
                        ]),

                    Section::make('C. SKRINING DENYUT NADI')
                        ->icon('heroicon-m-heart')
                        ->schema([
                            Grid::make(3)->schema([
                                TextEntry::make('resting_heart_rate')
                                    ->label('Nadi Istirahat')
                                    ->suffix(' /menit')
                                    ->placeholder('-'),
                                TextEntry::make('max_heart_rate')
                                    ->label('Nadi Maksimal')
                                    ->suffix(' /menit')
                                    ->placeholder('-'),
                                TextEntry::make('training_heart_rate')
                                    ->label('Nadi Latihan')
                                    ->suffix(' /menit')
                                    ->placeholder('-'),
                            ]),
                        ]),
                ]),

                // D. TABEL RINCIAN HASIL TES FISIK
                Section::make('D. RINCIAN HASIL TES FISIK')
                    ->icon('heroicon-m-clipboard-document-check')
                    ->schema([
                        ViewEntry::make('table_test')
                            ->view('filament.resources.student-performances.components.test-table'),
                    ]),

                // E. DATA VISUAL (Tambahkan bagian ini)
                Section::make('E. DATA VISUAL')
                    ->icon('heroicon-m-chart-pie')
                    ->schema([
                        ViewEntry::make('visual_data')
                            ->view('filament.resources.student-performances.components.visual-data'),
                    ]),

                // TIM PENGUJI / PELATIH
                Section::make('TIM PELATIH & PENGUJI')
                    ->compact()
                    ->schema([
                        Grid::make(3)->schema([
                            TextEntry::make('p1')
                                ->label('Pelatih Fisik')
                                ->default('HENNY WIJAYANTI')
                                ->weight('bold'),
                            TextEntry::make('p2')
                                ->label('Pelatih Teknik')
                                ->default('WULAN RAMADANI')
                                ->weight('bold'),
                            TextEntry::make('p3')
                                ->label('Head Coach')
                                ->default('M. TAQIYYUDIN ROHANAN')
                                ->weight('bold'),
                        ]),
                    ]),
            ]);
    }
}
