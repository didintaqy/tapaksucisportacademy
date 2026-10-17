<?php

namespace App\Filament\Resources\PaymentTypes\Schemas;

use App\Enums\PaymentPeriodType;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PaymentTypeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama Jenis Pembayaran')
                    ->required()
                    ->maxLength(255),

                TextInput::make('slug')
                    ->label('Slug')
                    ->helperText('Kosongkan untuk dibuat otomatis dari nama.')
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->nullable(),

                Select::make('period_type')
                    ->label('Tipe Periode')
                    ->options(PaymentPeriodType::class)
                    ->default(PaymentPeriodType::OneTime->value)
                    ->required()
                    ->native(false),

                TextInput::make('default_amount')
                    ->label('Nominal Bawaan')
                    ->numeric()
                    ->prefix('Rp')
                    ->default(0)
                    ->minValue(0),

                Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true),

                TextInput::make('sort_order')
                    ->label('Urutan')
                    ->numeric()
                    ->default(0),

                Textarea::make('description')
                    ->label('Deskripsi')
                    ->columnSpanFull()
                    ->rows(3),
            ]);
    }
}
