<?php

namespace App\Filament\Resources\Payments\Tables;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Payment;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class PaymentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('invoice_number')
                    ->label('No. Invoice')
                    ->searchable()
                    ->copyable()
                    ->sortable(),
                TextColumn::make('student.full_name')
                    ->label('Siswa')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('paymentType.name')
                    ->label('Jenis')
                    ->placeholder('-')
                    ->toggleable(),
                TextColumn::make('period')
                    ->label('Periode')
                    ->placeholder('-')
                    ->toggleable(),
                TextColumn::make('amount')
                    ->label('Nominal')
                    ->money('IDR')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->sortable(),
                TextColumn::make('payment_method')
                    ->label('Metode')
                    ->badge()
                    ->placeholder('-')
                    ->toggleable(),
                TextColumn::make('due_date')
                    ->label('Jatuh Tempo')
                    ->date('d M Y')
                    ->placeholder('-')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('paid_at')
                    ->label('Waktu Bayar')
                    ->dateTime('d M Y H:i')
                    ->placeholder('-')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(PaymentStatus::class)
                    ->multiple(),
                SelectFilter::make('payment_type_id')
                    ->label('Jenis Pembayaran')
                    ->relationship('paymentType', 'name'),
                SelectFilter::make('payment_method')
                    ->label('Metode Bayar')
                    ->options(PaymentMethod::class),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
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
                    })
                    ->successNotificationTitle('Pembayaran ditandai lunas'),
                Action::make('receipt')
                    ->label('Kwitansi')
                    ->icon('heroicon-o-printer')
                    ->color('gray')
                    ->url(fn (Payment $record): string => route('payments.receipt', $record))
                    ->openUrlInNewTab(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('mark_paid_bulk')
                        ->label('Tandai Lunas')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->form([
                            Select::make('payment_method')
                                ->label('Metode Bayar')
                                ->options(PaymentMethod::class)
                                ->default(PaymentMethod::Cash->value)
                                ->required(),
                        ])
                        ->action(function (Collection $records, array $data): void {
                            $method = PaymentMethod::from($data['payment_method']);
                            $records->each(fn (Payment $record) => $record->markAsPaid($method));
                        })
                        ->deselectRecordsAfterCompletion()
                        ->successNotificationTitle('Pembayaran terpilih ditandai lunas'),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
