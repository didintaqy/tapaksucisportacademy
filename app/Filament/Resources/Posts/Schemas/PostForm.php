<?php

namespace App\Filament\Resources\Posts\Schemas;

use App\Enums\PostStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class PostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Konten Artikel')
                    ->columns(2)
                    ->schema([
                        TextInput::make('title')
                            ->label('Judul')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (?string $state, Set $set, string $operation): void {
                                if ($operation === 'create' && filled($state)) {
                                    $set('slug', Str::slug($state));
                                }
                            })
                            ->columnSpanFull(),

                        TextInput::make('slug')
                            ->label('Slug')
                            ->helperText('Dibuat otomatis dari judul. Boleh diubah untuk URL yang lebih baik.')
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->nullable(),

                        TextInput::make('category')
                            ->label('Kategori')
                            ->maxLength(255)
                            ->placeholder('contoh: Latihan, Tips, Kejuaraan'),

                        Textarea::make('excerpt')
                            ->label('Ringkasan')
                            ->helperText('Ditampilkan pada daftar blog dan hasil pencarian.')
                            ->rows(3)
                            ->maxLength(500)
                            ->columnSpanFull(),

                        RichEditor::make('content')
                            ->label('Isi Artikel')
                            ->fileAttachmentsDisk('public')
                            ->fileAttachmentsDirectory('posts/content')
                            ->fileAttachmentsVisibility('public')
                            ->required()
                            ->columnSpanFull(),
                    ]),

                Section::make('Gambar & Penulis')
                    ->columns(2)
                    ->schema([
                        FileUpload::make('cover_image')
                            ->label('Gambar Sampul')
                            ->image()
                            ->disk('public')
                            ->directory('posts/covers')
                            ->visibility('public')
                            ->maxSize(5120)
                            ->acceptedFileTypes([
                                'image/jpeg',
                                'image/png',
                                'image/webp',
                            ])
                            ->helperText('Format JPG, PNG, atau WEBP. Maksimal 5 MB.')
                            ->columnSpanFull(),

                        Select::make('author_id')
                            ->label('Penulis')
                            ->relationship('author', 'name')
                            ->searchable()
                            ->preload()
                            ->default(fn () => auth()->id()),

                        TextInput::make('views')
                            ->label('Jumlah Dilihat')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->disabled()
                            ->dehydrated(false),
                    ]),

                Section::make('Publikasi')
                    ->columns(2)
                    ->schema([
                        Select::make('status')
                            ->label('Status')
                            ->options(PostStatus::class)
                            ->default(PostStatus::Draft->value)
                            ->required()
                            ->native(false),

                        DateTimePicker::make('published_at')
                            ->label('Tanggal Terbit')
                            ->default(now())
                            ->seconds(false),

                        Toggle::make('is_featured')
                            ->label('Jadikan Artikel Unggulan')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
