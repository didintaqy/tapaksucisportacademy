<?php

namespace App\Filament\Resources\Payments\Pages;

use App\Filament\Resources\Payments\PaymentResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPayment extends ViewRecord
{
    protected static string $resource = PaymentResource::class;

    public function getTitle(): string
    {
        return 'Detail Pembayaran: '.$this->record->invoice_number;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('receipt')
                ->label('Cetak Kwitansi')
                ->icon('heroicon-o-printer')
                ->color('gray')
                ->url(fn () => route('payments.receipt', $this->record))
                ->openUrlInNewTab(),
            EditAction::make(),
        ];
    }
}
