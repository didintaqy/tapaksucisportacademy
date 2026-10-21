<?php

namespace App\Filament\Resources\Newsletters\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class NewsletterForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Konten Newsletter')
                    ->schema([
                        TextInput::make('subject')
                            ->label('Subjek')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Contoh: Jadwal Latihan Bulan November'),

                        RichEditor::make('content')
                            ->label('Konten')
                            ->required()
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'underline',
                                'strike',
                                'link',
                                'bulletList',
                                'orderedList',
                                'blockquote',
                                'codeBlock',
                                'h2',
                                'h3',
                                'undo',
                                'redo',
                            ])
                            ->columnSpanFull(),

                        Toggle::make('is_draft')
                            ->label('Simpan sebagai Draf')
                            ->helperText('Jika dicentang, newsletter tidak akan dikirim saat tombol Kirim ditekan.')
                            ->default(true)
                            ->inline(false)
                            ->columnSpanFull(),
                    ])->columns(2),

                Section::make('Pengiriman')
                    ->schema([
                        Select::make('created_by')
                            ->label('Dibuat Oleh')
                            ->relationship('creator', 'name')
                            ->searchable()
                            ->preload()
                            ->default(auth()->id())
                            ->disabled(fn (string $context): bool => $context === 'edit'),

                        TextInput::make('recipients_count')
                            ->label('Jumlah Penerima')
                            ->numeric()
                            ->default(0)
                            ->disabled()
                            ->dehydrated(false)
                            ->helperText('Terisi otomatis saat newsletter dikirim.'),

                        DateTimePicker::make('sent_at')
                            ->label('Dikirim Pada')
                            ->disabled()
                            ->dehydrated(false)
                            ->helperText('Terisi otomatis saat newsletter dikirim.'),
                    ])->columns(2)->visible(fn (string $context): bool => $context === 'edit'),
            ]);
    }
}
