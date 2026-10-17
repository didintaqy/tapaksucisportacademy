<?php

namespace App\Filament\Resources\Galleries\Schemas;

use App\Models\Gallery;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class GalleryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Foto Kegiatan')
                    ->columns(2)
                    ->schema([
                        FileUpload::make('image')
                            ->label('Foto')
                            ->image()
                            ->disk('public')
                            ->directory('gallery')
                            ->visibility('public')
                            ->maxSize(5120)
                            ->acceptedFileTypes([
                                'image/jpeg',
                                'image/png',
                                'image/webp',
                            ])
                            ->helperText('Format JPG, PNG, atau WEBP. Maksimal 5 MB.')
                            ->required()
                            ->columnSpanFull(),

                        TextInput::make('title')
                            ->label('Judul')
                            ->maxLength(255)
                            ->placeholder('contoh: Kejuaraan Antar Dojo 2026'),

                        TextInput::make('category')
                            ->label('Kategori')
                            ->datalist(
                                fn (): array => Gallery::query()
                                    ->whereNotNull('category')
                                    ->distinct()
                                    ->orderBy('category')
                                    ->pluck('category')
                                    ->all()
                            )
                            ->helperText('contoh: Latihan Rutin, Kejuaraan, Ujian Tingkat, Kegiatan Sosial')
                            ->maxLength(255),

                        Textarea::make('description')
                            ->label('Deskripsi')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),

                Section::make('Publikasi')
                    ->columns(2)
                    ->schema([
                        DatePicker::make('taken_at')
                            ->label('Tanggal Kegiatan')
                            ->default(now()),

                        TextInput::make('sort_order')
                            ->label('Urutan')
                            ->numeric()
                            ->default(0)
                            ->helperText('Angka kecil tampil lebih dahulu.'),

                        Toggle::make('is_published')
                            ->label('Tampilkan di Website')
                            ->default(true)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
