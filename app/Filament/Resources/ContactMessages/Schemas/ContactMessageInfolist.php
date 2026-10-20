<?php

namespace App\Filament\Resources\ContactMessages\Schemas;

use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ContactMessageInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Pengirim')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('name')
                            ->label('Nama')
                            ->copyable(),

                        TextEntry::make('email')
                            ->label('Email')
                            ->copyable(),

                        TextEntry::make('phone')
                            ->label('Telepon')
                            ->placeholder('-')
                            ->copyable(),

                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'unread' => 'danger',
                                'read' => 'warning',
                                'replied' => 'success',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'unread' => 'Belum Dibaca',
                                'read' => 'Sudah Dibaca',
                                'replied' => 'Sudah Dibalas',
                                default => $state,
                            }),
                    ]),

                Section::make('Pesan')
                    ->schema([
                        TextEntry::make('message')
                            ->label('Isi Pesan')
                            ->columnSpanFull()
                            ->prose(),
                    ]),

                Section::make('Log Aktivitas')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Diterima')
                            ->dateTime('d M Y H:i')
                            ->copyable(),

                        TextEntry::make('read_at')
                            ->label('Dibaca Pada')
                            ->dateTime('d M Y H:i')
                            ->placeholder('-')
                            ->copyable(),

                        TextEntry::make('replied_at')
                            ->label('Dibalas Pada')
                            ->dateTime('d M Y H:i')
                            ->placeholder('-')
                            ->copyable(),
                    ]),
            ]);
    }
}
