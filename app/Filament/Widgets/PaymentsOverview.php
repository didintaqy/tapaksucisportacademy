<?php

namespace App\Filament\Widgets;

use App\Models\Payment;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PaymentsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        $startOfMonth = now()->startOfMonth();

        $incomeThisMonth = (float) Payment::paid()
            ->where('paid_at', '>=', $startOfMonth)
            ->sum('amount');

        $paidCount = Payment::paid()
            ->where('paid_at', '>=', $startOfMonth)
            ->count();

        $outstanding = (float) Payment::outstanding()->sum('amount');
        $outstandingCount = Payment::outstanding()->count();

        $collected = (float) Payment::paid()->sum('amount');

        return [
            Stat::make('Pemasukan Bulan Ini', 'Rp '.number_format($incomeThisMonth, 0, ',', '.'))
                ->description($paidCount.' transaksi lunas')
                ->descriptionIcon('heroicon-m-arrow-trending-up', IconPosition::Before)
                ->color('success'),

            Stat::make('Tunggakan', 'Rp '.number_format($outstanding, 0, ',', '.'))
                ->description($outstandingCount.' tagihan belum lunas')
                ->descriptionIcon('heroicon-m-exclamation-triangle', IconPosition::Before)
                ->color('danger'),

            Stat::make('Total Terkumpul', 'Rp '.number_format($collected, 0, ',', '.'))
                ->description('Akumulasi seluruh pembayaran lunas')
                ->descriptionIcon('heroicon-m-banknotes', IconPosition::Before)
                ->color('primary'),
        ];
    }
}
