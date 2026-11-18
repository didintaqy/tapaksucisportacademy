<?php

namespace App\Filament\Resources\Students\Schemas;

use App\Enums\BeltRank;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class StudentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('student_code')
                    ->label('Kode Siswa')
                    ->disabled()
                    ->dehydrated(false)
                    ->placeholder('Dibuat otomatis')
                    ->helperText('Kode dibuat otomatis setelah siswa disimpan.'),
                TextInput::make('full_name')
                    ->label('Nama Lengkap')
                    ->required()
                    ->maxLength(255),
                TextInput::make('nickname')
                    ->label('Nama Panggilan')
                    ->maxLength(255),
                TextInput::make('birth_place')
                    ->label('Tempat Lahir')
                    ->maxLength(255),
                DatePicker::make('birth_date')
                    ->label('Tanggal Lahir')
                    ->native(false)
                    ->displayFormat('d/m/Y'),
                TextInput::make('nik')
                    ->label('NIK')
                    ->maxLength(16)
                    ->unique(ignoreRecord: true),
                TextInput::make('family_card_number')
                    ->label('No. Kartu Keluarga')
                    ->maxLength(16),
                TextInput::make('phone_number')
                    ->label('No. Telepon')
                    ->tel()
                    ->maxLength(20),
                TextInput::make('email')
                    ->label('Email Wali/Siswa')
                    ->email()
                    ->maxLength(255)
                    ->helperText('Digunakan untuk pengingat tagihan otomatis.'),
                Select::make('gender')
                    ->label('Jenis Kelamin')
                    ->options([
                        'L' => 'Laki-laki',
                        'P' => 'Perempuan',
                    ])
                    ->native(false),
                Select::make('belt_level')
                    ->label('Tingkat Sabuk')
                    ->options(BeltRank::options())
                    ->native(false),
                TextInput::make('branch')
                    ->label('Cabang')
                    ->maxLength(255)
                    ->placeholder('Contoh: Cabang Pusat'),
                Select::make('program_id')
                    ->label('Program Latihan')
                    ->relationship('program', 'name')
                    ->searchable()
                    ->preload()
                    ->placeholder('Pilih program'),
                Select::make('education')
                    ->label('Pendidikan')
                    ->options([
                        'SD' => 'SD',
                        'SMP' => 'SMP',
                        'SMA' => 'SMA',
                        'D3' => 'D3',
                        'S1' => 'S1',
                        'S2' => 'S2',
                        'S3' => 'S3',
                    ])
                    ->native(false),
                Textarea::make('address')
                    ->label('Alamat')
                    ->columnSpanFull(),
                FileUpload::make('photo')
                    ->avatar()
                    ->label('Foto Terbaru Seragam Tapak Suci')
                    ->image()
                    ->imageEditor()
                    ->disk('public')
                    ->directory('students')
                    ->visibility('public')
                    ->nullable()
                    ->maxSize(5120)
                    ->acceptedFileTypes([
                        'image/jpeg',
                        'image/png',
                        'image/webp',
                    ])
                    ->helperText('Format JPG, PNG, atau WEBP. Maksimal 5 MB.'),
            ]);
    }
}
