<?php

namespace App\Filament\Resources\Payments\Pages;

use App\Filament\Resources\Payments\PaymentResource;
use App\Filament\Resources\PaymentTypes\PaymentTypeResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;

class ListPayments extends ListRecords
{
    protected static string $resource = PaymentResource::class;

    public function getTitle(): string|Htmlable
    {
        return 'Pembayaran Siswa';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('payment_types')
                ->label('Jenis Pembayaran')
                ->icon('heroicon-o-banknotes')
                ->color('gray')
                ->url(PaymentTypeResource::getUrl('index')),
            CreateAction::make()
                ->label('Tambah Pembayaran'),
        ];
    }
}
