<?php

namespace App\Filament\Widgets;

use App\Enums\UserRole;
use App\Models\Payment;
use Filament\Widgets\ChartWidget;

class IncomeChart extends ChartWidget
{
    protected static ?int $sort = 2;

    public static function canView(): bool
    {
        return auth()->user()?->hasAnyRole(UserRole::Admin, UserRole::Operator) ?? false;
    }

    protected ?string $heading = 'Pemasukan 6 Bulan Terakhir';

    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        $labels = [];
        $paid = [];
        $outstanding = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i)->startOfMonth();
            $labels[] = $month->locale('id')->translatedFormat('M Y');
            $paid[] = (float) Payment::paid()
                ->whereBetween('paid_at', [$month, $month->copy()->endOfMonth()])
                ->sum('amount');
            $outstanding[] = (float) Payment::outstanding()
                ->where('period', $month->format('Y-m'))
                ->sum('amount');
        }

        return [
            'datasets' => [
                [
                    'label' => 'Lunas',
                    'data' => $paid,
                    'backgroundColor' => '#10B981',
                ],
                [
                    'label' => 'Belum Lunas',
                    'data' => $outstanding,
                    'backgroundColor' => '#F43F5E',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
