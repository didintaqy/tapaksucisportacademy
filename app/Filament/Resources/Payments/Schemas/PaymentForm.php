<?php

namespace App\Filament\Resources\Payments\Schemas;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\PaymentType;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class PaymentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Tagihan')
                    ->columns(2)
                    ->schema([
                        Select::make('student_id')
                            ->label('Siswa')
                            ->relationship('student', 'full_name')
                            ->getOptionLabelFromRecordUsing(
                                fn ($record) => $record->full_name.($record->nickname ? " ({$record->nickname})" : '')
                            )
                            ->searchable(['full_name', 'nickname'])
                            ->preload()
                            ->required(),

                        Select::make('payment_type_id')
                            ->label('Jenis Pembayaran')
                            ->relationship('paymentType', 'name')
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(function ($state, Set $set): void {
                                if ($state && $type = PaymentType::find($state)) {
                                    $set('amount', (float) $type->default_amount);
                                }
                            }),

                        TextInput::make('period')
                            ->label('Periode')
                            ->placeholder('contoh: 2026-10')
                            ->helperText('Diisi untuk iuran bulanan, contoh 2026-10.'),

                        TextInput::make('amount')
                            ->label('Nominal')
                            ->numeric()
                            ->prefix('Rp')
                            ->minValue(0)
                            ->required(),

                        TextInput::make('description')
                            ->label('Keterangan')
                            ->columnSpanFull(),
                    ]),

                Section::make('Status & Pembayaran')
                    ->columns(2)
                    ->schema([
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

                        DateTimePicker::make('paid_at')
                            ->label('Waktu Bayar'),

                        FileUpload::make('proof_file')
                            ->label('Bukti Bayar')
                            ->disk('public')
                            ->directory('payments/proofs')
                            ->visibility('public')
                            ->maxSize(5120)
                            ->acceptedFileTypes([
                                'image/jpeg',
                                'image/png',
                                'image/webp',
                                'application/pdf',
                            ])
                            ->helperText('Format JPG, PNG, WEBP, atau PDF. Maksimal 5 MB.')
                            ->columnSpanFull(),

                        Textarea::make('notes')
                            ->label('Catatan')
                            ->columnSpanFull()
                            ->rows(3),
                    ]),
            ]);
    }
}
