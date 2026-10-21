<?php

namespace App\Filament\Resources\Payments\Tables;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Mail\PaymentReminderMail;
use App\Models\Payment;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Mail;

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
                TextColumn::make('reminded_at')
                    ->label('Pengingat Terakhir')
                    ->dateTime('d M Y H:i')
                    ->placeholder('Belum pernah')
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
                Action::make('send_reminder')
                    ->label('Pengingat')
                    ->icon('heroicon-o-envelope')
                    ->color('warning')
                    ->visible(fn (Payment $record): bool => $record->isOutstanding() && filled($record->student?->email))
                    ->requiresConfirmation()
                    ->modalHeading('Kirim pengingat tagihan')
                    ->modalDescription(fn (Payment $record): string => 'Kirim email pengingat ke '.$record->student->email.'?')
                    ->action(function (Payment $record): void {
                        Mail::to($record->student->email)
                            ->queue(new PaymentReminderMail($record, self::daysUntilDue($record)));

                        $record->markAsReminded();

                        Notification::make()
                            ->title('Pengingat tagihan dikirim')
                            ->success()
                            ->send();
                    }),
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
                    BulkAction::make('send_reminders_bulk')
                        ->label('Kirim Pengingat')
                        ->icon('heroicon-o-envelope')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->action(function (Collection $records): void {
                            $sent = 0;
                            $skipped = 0;

                            foreach ($records as $record) {
                                if (! $record->isOutstanding() || blank($record->student?->email)) {
                                    $skipped++;

                                    continue;
                                }

                                Mail::to($record->student->email)
                                    ->queue(new PaymentReminderMail($record, self::daysUntilDue($record)));

                                $record->markAsReminded();

                                $sent++;
                            }

                            Notification::make()
                                ->title($sent.' pengingat dikirim')
                                ->body($skipped.' dilewati (sudah lunas atau tanpa email).')
                                ->status($sent > 0 ? 'success' : 'warning')
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    private static function daysUntilDue(Payment $payment): int
    {
        if ($payment->due_date === null) {
            return 0;
        }

        return (int) now()
            ->startOfDay()
            ->diffInDays($payment->due_date->copy()->startOfDay(), false);
    }
}
