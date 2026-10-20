<?php

namespace App\Filament\Resources\Events\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
use Filament\Schemas\Schema;

class EventForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Judul Kegiatan')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Contoh: Kejuaraan Regional Sumatera'),

                Textarea::make('description')
                    ->label('Deskripsi')
                    ->rows(3)
                    ->placeholder('Deskripsi detail kegiatan...')
                    ->columnSpanFull(),

                DateTimePicker::make('starts_at')
                    ->label('Mulai')
                    ->required()
                    ->displayFormat('d/m/Y H:i')
                    ->native(false)
                    ->seconds(false),

                DateTimePicker::make('ends_at')
                    ->label('Selesai')
                    ->displayFormat('d/m/Y H:i')
                    ->native(false)
                    ->seconds(false)
                    ->after('starts_at'),

                Select::make('type')
                    ->label('Jenis Kegiatan')
                    ->options([
                        'latihan' => 'Latihan',
                        'kejuaraan' => 'Kejuaraan',
                        'ukt' => 'UKT',
                        'pengumuman' => 'Pengumuman',
                        'libur' => 'Libur',
                    ])
                    ->default('latihan')
                    ->required()
                    ->native(false),

                Toggle::make('is_public')
                    ->label('Tampilkan di Kalender Publik')
                    ->default(true)
                    ->helperText('Jika tidak dicentang, kegiatan hanya terlihat di admin'),

                TextInput::make('created_by')
                    ->label('Dibuat Oleh')
                    ->numeric()
                    ->default(auth()->id())
                    ->disabled()
                    ->dehydrated()
                    ->visible(fn (Get $get) => $get('created_by') ?? false),
            ]);
    }
}
