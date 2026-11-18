<?php

namespace App\Filament\Resources\Items\Schemas;

use App\Models\Item;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Data Barang')
                    ->columns(2)
                    ->schema([
                        TextInput::make('code')
                            ->label('Kode')
                            ->required()
                            ->maxLength(40)
                            ->unique(ignoreRecord: true),

                        TextInput::make('name')
                            ->label('Nama Barang')
                            ->required()
                            ->maxLength(255),

                        Select::make('category')
                            ->label('Kategori')
                            ->options(Item::CATEGORIES)
                            ->default('seragam')
                            ->required(),

                        TextInput::make('unit')
                            ->label('Satuan')
                            ->default('pcs')
                            ->maxLength(20)
                            ->required(),

                        TextInput::make('price')
                            ->label('Harga')
                            ->numeric()
                            ->minValue(0)
                            ->prefix('Rp')
                            ->default(0),

                        TextInput::make('min_stock')
                            ->label('Stok Minimum')
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->helperText('Peringatan muncul bila stok di bawah/ sama dengan nilai ini.'),

                        TextInput::make('stock')
                            ->label('Stok Saat Ini')
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->helperText('Untuk perubahan yang terlacak, gunakan menu Pergerakan Stok.'),

                        Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true),
                    ]),
            ]);
    }
}
