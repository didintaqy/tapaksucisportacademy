<?php

namespace App\Filament\Resources\Students\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class StudentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('full_name')
                    ->required(),
                TextInput::make('nickname'),
                TextInput::make('birth_place'),
                DatePicker::make('birth_date'),
                TextInput::make('nik'),
                TextInput::make('family_card_number'),
                TextInput::make('phone_number')
                    ->tel(),
                TextInput::make('gender'),
                TextInput::make('belt_level'),
                Textarea::make('address')
                    ->columnSpanFull(),
                TextInput::make('education'),
                TextInput::make('photo'),
            ]);
    }
}
