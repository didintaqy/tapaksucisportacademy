<?php

namespace App\Filament\Resources\Students\RelationManagers;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Payment;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'payments';

    protected static ?string $title = 'Riwayat Pembayaran';

    protected static string|\BackedEnum|null $icon = 'heroicon-m-credit-card';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('payment_type_id')
                    ->label('Jenis Pembayaran')
                    ->relationship('paymentType', 'name')
                    ->searchable()
                    ->preload(),
                TextInput::make('period')
                    ->label('Periode')
                    ->placeholder('contoh: 2026-10'),
                TextInput::make('amount')
                    ->label('Nominal')
                    ->numeric()
                    ->prefix('Rp')
                    ->required(),
                Select::make('status')
                    ->label('Status')
                    ->options(PaymentStatus::class)
                    ->default(PaymentStatus::Unpaid->value)
                    ->required()
                    ->native(false),
                Select::make('payment_method')
                    ->label('Metode Bayar')
                    ->options(PaymentMethod::class)
                    ->native(false),
                DatePicker::make('due_date')
                    ->label('Jatuh Tempo'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('invoice_number')
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('invoice_number')
                    ->label('No. Invoice')
                    ->searchable(),
                TextColumn::make('paymentType.name')
                    ->label('Jenis')
                    ->placeholder('-'),
                TextColumn::make('period')
                    ->label('Periode')
                    ->placeholder('-'),
                TextColumn::make('amount')
                    ->label('Nominal')
                    ->money('IDR'),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge(),
                TextColumn::make('due_date')
                    ->label('Jatuh Tempo')
                    ->date('d M Y')
                    ->placeholder('-'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Tambah Tagihan'),
            ])
            ->recordActions([
                Action::make('mark_paid')
                    ->label('Tandai Lunas')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Payment $record): bool => ! $record->isPaid())
                    ->form([
                        Select::make('payment_method')
                            ->label('Metode Bayar')
                            ->options(PaymentMethod::class)
                            ->default(PaymentMethod::Cash->value)
                            ->required(),
                    ])
                    ->action(function (array $data, Payment $record): void {
                        $record->markAsPaid(PaymentMethod::from($data['payment_method']));
                    }),
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
