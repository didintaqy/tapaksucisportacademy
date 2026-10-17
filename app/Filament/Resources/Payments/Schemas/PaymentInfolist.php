<?php

namespace App\Filament\Resources\Payments\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PaymentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Tagihan')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('invoice_number')
                            ->label('No. Invoice')
                            ->copyable(),
                        TextEntry::make('student.full_name')
                            ->label('Siswa'),
                        TextEntry::make('paymentType.name')
                            ->label('Jenis Pembayaran')
                            ->placeholder('-'),
                        TextEntry::make('period')
                            ->label('Periode')
                            ->placeholder('-'),
                        TextEntry::make('amount')
                            ->label('Nominal')
                            ->money('IDR'),
                        TextEntry::make('due_date')
                            ->label('Jatuh Tempo')
                            ->date('d F Y')
                            ->placeholder('-'),
                    ]),

                Section::make('Status Pembayaran')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('status')
                            ->label('Status')
                            ->badge(),
                        TextEntry::make('payment_method')
                            ->label('Metode Bayar')
                            ->badge()
                            ->placeholder('-'),
                        TextEntry::make('paid_at')
                            ->label('Waktu Bayar')
                            ->dateTime('d F Y H:i')
                            ->placeholder('-'),
                        TextEntry::make('created_at')
                            ->label('Dibuat')
                            ->dateTime('d F Y H:i'),
                    ]),

                Section::make('Keterangan & Bukti')
                    ->schema([
                        TextEntry::make('description')
                            ->label('Keterangan')
                            ->placeholder('-')
                            ->columnSpanFull(),
                        TextEntry::make('notes')
                            ->label('Catatan')
                            ->placeholder('-')
                            ->columnSpanFull(),
                        ImageEntry::make('proof_file')
                            ->label('Bukti Bayar')
                            ->disk('public')
                            ->visible(fn ($record) => $record->proof_file && ! str_ends_with((string) $record->proof_file, '.pdf')),
                    ]),

                Section::make('Data Gateway')
                    ->columns(2)
                    ->collapsed()
                    ->visible(fn ($record) => filled($record->gateway_name))
                    ->schema([
                        TextEntry::make('gateway_name')
                            ->label('Gateway'),
                        TextEntry::make('gateway_reference')
                            ->label('Referensi'),
                    ]),
            ]);
    }
}
