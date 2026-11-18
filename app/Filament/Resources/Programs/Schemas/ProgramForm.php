<?php

namespace App\Filament\Resources\Programs\Schemas;

use App\Enums\ProgramLevel;
use App\Models\Program;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ProgramForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Program')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Program')
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
                            ->helperText('Dibuat otomatis dari nama. Boleh diubah untuk URL yang lebih baik.')
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        Select::make('level')
                            ->label('Tingkat')
                            ->options(ProgramLevel::class)
                            ->default(ProgramLevel::All->value)
                            ->required()
                            ->native(false),

                        Textarea::make('short_description')
                            ->label('Deskripsi Singkat')
                            ->helperText('Tampil pada kartu program dan hasil pencarian.')
                            ->rows(2)
                            ->maxLength(500)
                            ->columnSpanFull(),

                        RichEditor::make('description')
                            ->label('Deskripsi Lengkap')
                            ->toolbarButtons([
                                'bold', 'italic', 'underline', 'strike', 'link',
                                'bulletList', 'orderedList', 'blockquote',
                                'h2', 'h3', 'undo', 'redo',
                            ])
                            ->columnSpanFull(),
                    ]),

                Section::make('Detail Latihan')
                    ->columns(3)
                    ->schema([
                        TextInput::make('age_min')
                            ->label('Usia Minimal')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->suffix('tahun'),

                        TextInput::make('age_max')
                            ->label('Usia Maksimal')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->gte('age_min')
                            ->suffix('tahun'),

                        TextInput::make('sessions_per_week')
                            ->label('Frekuensi per Minggu')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(14)
                            ->suffix('kali'),

                        TextInput::make('session_duration_minutes')
                            ->label('Durasi per Sesi')
                            ->numeric()
                            ->minValue(15)
                            ->maxValue(300)
                            ->suffix('menit'),

                        TextInput::make('monthly_fee')
                            ->label('Iuran Bulanan')
                            ->numeric()
                            ->minValue(0)
                            ->prefix('Rp')
                            ->helperText('Dipakai saat generate tagihan SPP siswa program ini.'),

                        TagsInput::make('branches')
                            ->label('Cabang')
                            ->placeholder('Tambah cabang...')
                            ->suggestions(fn (): array => array_values(Program::branchOptions()))
                            ->helperText('Kosongkan jika program tersedia di semua cabang.')
                            ->columnSpanFull(),
                    ]),

                Section::make('Publikasi')
                    ->columns(2)
                    ->schema([
                        FileUpload::make('cover_image')
                            ->label('Gambar Sampul')
                            ->image()
                            ->imageEditor()
                            ->disk('public')
                            ->directory('programs/covers')
                            ->visibility('public')
                            ->maxSize(5120)
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->helperText('Format JPG, PNG, atau WEBP. Maksimal 5 MB.')
                            ->columnSpanFull(),

                        Toggle::make('is_active')
                            ->label('Tampilkan di website')
                            ->default(true),

                        Toggle::make('is_featured')
                            ->label('Jadikan Program Unggulan')
                            ->helperText('Program unggulan tampil di beranda.'),

                        TextInput::make('sort_order')
                            ->label('Urutan Tampil')
                            ->numeric()
                            ->default(0)
                            ->minValue(0),
                    ]),

                Section::make('SEO')
                    ->collapsed()
                    ->columns(1)
                    ->schema([
                        TextInput::make('meta_title')
                            ->label('Meta Title')
                            ->maxLength(255),

                        Textarea::make('meta_description')
                            ->label('Meta Description')
                            ->rows(2)
                            ->maxLength(500),
                    ]),
            ]);
    }
}
