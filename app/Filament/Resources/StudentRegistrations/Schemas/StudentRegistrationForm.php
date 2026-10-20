<?php

namespace App\Filament\Resources\StudentRegistrations\Schemas;

use App\Enums\RegistrationStatus;
use App\Models\StudentRegistration;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class StudentRegistrationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('full_name')
                    ->label('Nama Lengkap')
                    ->required()
                    ->maxLength(255),

                TextInput::make('nickname')
                    ->label('Nama Panggilan')
                    ->maxLength(255),

                Select::make('gender')
                    ->label('Jenis Kelamin')
                    ->options(StudentRegistration::GENDERS)
                    ->required()
                    ->native(false),

                TextInput::make('birth_place')
                    ->label('Tempat Lahir')
                    ->maxLength(255),

                DatePicker::make('birth_date')
                    ->label('Tanggal Lahir')
                    ->required(),

                TextInput::make('education')
                    ->label('Pendidikan')
                    ->maxLength(255),

                TextInput::make('parent_name')
                    ->label('Nama Orang Tua / Wali')
                    ->required()
                    ->maxLength(255),

                TextInput::make('phone')
                    ->label('Nomor Telepon')
                    ->tel()
                    ->required()
                    ->maxLength(30),

                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->maxLength(255),

                TextInput::make('branch')
                    ->label('Cabang yang Diinginkan')
                    ->maxLength(255),

                Textarea::make('address')
                    ->label('Alamat')
                    ->required()
                    ->rows(3)
                    ->columnSpanFull(),

                Textarea::make('notes')
                    ->label('Catatan dari Pendaftar')
                    ->rows(3)
                    ->columnSpanFull(),

                Select::make('status')
                    ->label('Status')
                    ->options(RegistrationStatus::class)
                    ->default(RegistrationStatus::New->value)
                    ->required()
                    ->native(false),

                Textarea::make('admin_notes')
                    ->label('Catatan Admin')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }
}
