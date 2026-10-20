<?php

namespace App\Filament\Resources\Testimonials\Schemas;

use App\Enums\TestimonialStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class TestimonialForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama')
                    ->required()
                    ->maxLength(255),

                TextInput::make('relation')
                    ->label('Keterangan')
                    ->placeholder('Contoh: Orang Tua Siswa')
                    ->maxLength(255),

                Select::make('rating')
                    ->label('Rating')
                    ->options([
                        1 => '1 bintang',
                        2 => '2 bintang',
                        3 => '3 bintang',
                        4 => '4 bintang',
                        5 => '5 bintang',
                    ])
                    ->default(5)
                    ->required()
                    ->native(false),

                Textarea::make('content')
                    ->label('Isi Testimoni')
                    ->required()
                    ->rows(5)
                    ->maxLength(2000)
                    ->columnSpanFull(),

                Select::make('status')
                    ->label('Status')
                    ->options(TestimonialStatus::class)
                    ->default(TestimonialStatus::Pending->value)
                    ->required()
                    ->native(false),

                Toggle::make('is_featured')
                    ->label('Tampilkan sebagai unggulan')
                    ->default(false),

                DateTimePicker::make('published_at')
                    ->label('Tanggal Tayang')
                    ->helperText('Kosongkan untuk otomatis saat disetujui.'),
            ]);
    }
}
