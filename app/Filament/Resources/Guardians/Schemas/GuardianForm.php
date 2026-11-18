<?php

namespace App\Filament\Resources\Guardians\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class GuardianForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Data Wali')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Lengkap')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('phone')
                            ->label('Telepon / WhatsApp')
                            ->tel()
                            ->maxLength(40),

                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->maxLength(255),

                        TextInput::make('occupation')
                            ->label('Pekerjaan')
                            ->maxLength(255),

                        Textarea::make('address')
                            ->label('Alamat')
                            ->rows(3)
                            ->columnSpanFull(),

                        Select::make('students')
                            ->label('Siswa Terhubung')
                            ->multiple()
                            ->relationship('students', 'full_name')
                            ->searchable(['full_name', 'student_code'])
                            ->preload()
                            ->helperText('Relasi default tersimpan sebagai "wali" dan bukan kontak utama. Ubah di pivot bila perlu.')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
