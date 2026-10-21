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

                        Select::make('category_id')
                            ->label('Kategori')
                            ->relationship('categoryRelation', 'name')
                            ->searchable()
                            ->preload()
                            ->placeholder('Pilih kategori')
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->label('Nama Kategori')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (?string $state, Set $set): void {
                                        if (filled($state)) {
                                            $set('slug', Str::slug($state));
                                        }
                                    }),
                                TextInput::make('slug')
                                    ->label('Slug')
                                    ->helperText('Dibuat otomatis dari nama. Boleh diubah.')
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true),
                                Textarea::make('description')
                                    ->label('Deskripsi')
                                    ->rows(2)
                                    ->maxLength(500),
                            ]),

                        Select::make('tags')
                            ->label('Tag')
                            ->relationship('tags', 'name')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->placeholder('Pilih tag (bisa banyak)')
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->label('Nama Tag')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (?string $state, Set $set): void {
                                        if (filled($state)) {
                                            $set('slug', Str::slug($state));
                                        }
                                    }),
                                TextInput::make('slug')
                                    ->label('Slug')
                                    ->helperText('Dibuat otomatis dari nama. Boleh diubah.')
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true),
                            ]),

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
                            ->default(fn () => auth()->id())
                            ->disabled(fn (): bool => ! auth()->user()?->isAdmin())
                            ->dehydrated(),

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
                            ->options(fn () => auth()->user()?->isAdmin()
                                ? PostStatus::class
                                : [
                                    PostStatus::Draft->value => PostStatus::Draft->getLabel(),
                                    PostStatus::Review->value => PostStatus::Review->getLabel(),
                                ])
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
