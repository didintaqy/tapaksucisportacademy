<?php

namespace App\Filament\Resources\ContactMessages\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ContactMessageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama')
                    ->disabled()
                    ->columnSpanFull(),

                TextInput::make('email')
                    ->label('Email')
                    ->disabled()
                    ->columnSpanFull(),

                TextInput::make('phone')
                    ->label('Telepon')
                    ->disabled()
                    ->columnSpanFull(),

                Textarea::make('message')
                    ->label('Pesan')
                    ->disabled()
                    ->rows(6)
                    ->columnSpanFull(),

                Select::make('status')
                    ->label('Status')
                    ->options([
                        'unread' => 'Belum Dibaca',
                        'read' => 'Sudah Dibaca',
                        'replied' => 'Sudah Dibalas',
                    ])
                    ->default('unread')
                    ->required()
                    ->native(false)
                    ->columnSpanFull(),
            ]);
    }
}
