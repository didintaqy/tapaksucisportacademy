<?php

namespace App\Filament\Resources\StockMovements\Schemas;

use App\Enums\StockMovementType;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class StockMovementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Pergerakan Stok')
                    ->columns(2)
                    ->schema([
                        Select::make('item_id')
                            ->label('Barang')
                            ->relationship('item', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('type')
                            ->label('Jenis')
                            ->options(collect(StockMovementType::cases())
                                ->mapWithKeys(fn (StockMovementType $type): array => [$type->value => $type->getLabel()])
                                ->all())
                            ->default(StockMovementType::In->value)
                            ->required(),

                        TextInput::make('quantity')
                            ->label('Jumlah')
                            ->numeric()
                            ->minValue(1)
                            ->required()
                            ->helperText('Untuk "Penyesuaian", nilai ini menjadi stok akhir.'),

                        TextInput::make('unit_price')
                            ->label('Harga Satuan')
                            ->numeric()
                            ->minValue(0)
                            ->prefix('Rp'),

                        TextInput::make('reference')
                            ->label('Referensi')
                            ->maxLength(120)
                            ->placeholder('Contoh: nota pembelian'),

                        DateTimePicker::make('moved_at')
                            ->label('Waktu')
                            ->default(now())
                            ->required(),

                        Textarea::make('notes')
                            ->label('Catatan')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
