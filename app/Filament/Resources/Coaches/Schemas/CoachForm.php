<?php

namespace App\Filament\Resources\Coaches\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CoachForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Profil Pelatih')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Lengkap')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('title')
                            ->label('Jabatan')
                            ->placeholder('Contoh: Pelatih Kepala')
                            ->maxLength(255),

                        TextInput::make('specialization')
                            ->label('Keahlian')
                            ->placeholder('Contoh: Seni Tunggal, Tanding')
                            ->maxLength(255),

                        TextInput::make('experience_years')
                            ->label('Pengalaman')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(80)
                            ->suffix('tahun'),

                        Textarea::make('bio')
                            ->label('Bio Singkat')
                            ->rows(4)
                            ->maxLength(1000)
                            ->columnSpanFull(),

                        FileUpload::make('photo_path')
                            ->label('Foto')
                            ->image()
                            ->disk('public')
                            ->directory('coaches')
                            ->visibility('public')
                            ->maxSize(5120)
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->helperText('Opsional. Jika kosong, kartu pelatih menampilkan inisial nama. Maksimal 5 MB.')
                            ->columnSpanFull(),

                        TextInput::make('sort_order')
                            ->label('Urutan Tampil')
                            ->numeric()
                            ->default(0)
                            ->minValue(0),

                        Toggle::make('is_active')
                            ->label('Tampilkan di website')
                            ->default(true),
                    ]),
            ]);
    }
}
