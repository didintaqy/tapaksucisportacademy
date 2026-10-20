<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\StudentPerformances\StudentPerformanceResource;
use App\Models\Student;
use App\Models\StudentPerformance;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PerformanceOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 5;

    protected function getStats(): array
    {
        $missing = Student::query()->whereDoesntHave('performances')->count();
        $vo2 = StudentPerformance::query()->whereNotNull('beep_test_vo2max')->avg('beep_test_vo2max');
        $pushUp = StudentPerformance::query()->whereNotNull('push_up_1_min')->avg('push_up_1_min');

        return [
            Stat::make('Belum Ada Data Fisik', (string) $missing)
                ->description('Siswa tanpa catatan performa')
                ->descriptionIcon('heroicon-m-exclamation-circle', IconPosition::Before)
                ->color($missing > 0 ? 'warning' : 'success')
                ->url(StudentPerformanceResource::getUrl()),

            Stat::make('Rata-rata VO2max', $vo2 === null ? '-' : number_format((float) $vo2, 1, ',', '.'))
                ->description('Dari beep test')
                ->descriptionIcon('heroicon-m-fire', IconPosition::Before)
                ->color('info')
                ->url(StudentPerformanceResource::getUrl()),

            Stat::make('Rata-rata Push-up', $pushUp === null ? '-' : number_format((float) $pushUp, 0, ',', '.'))
                ->description('Tes 1 menit')
                ->descriptionIcon('heroicon-m-bolt', IconPosition::Before)
                ->color('primary')
                ->url(StudentPerformanceResource::getUrl()),
        ];
    }
}
