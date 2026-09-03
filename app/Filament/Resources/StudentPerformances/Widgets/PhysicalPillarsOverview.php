<?php

namespace App\Filament\Resources\StudentPerformances\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Model;

class PhysicalPillarsOverview extends StatsOverviewWidget
{
    public ?Model $record = null;

    protected function getStats(): array
    {
        $perf = $this->record;

        $flex = $perf?->sit_and_reach ? min(round(($perf->sit_and_reach / 28) * 100), 100) : 0;
        
        $push = $perf?->push_up_1_min ? ($perf->push_up_1_min / 24) * 100 : 0;
        $sit = $perf?->sit_up_2_min ? ($perf->sit_up_2_min / 45) * 100 : 0;
        $core = $perf?->core_stability_level ? ($perf->core_stability_level / 12) * 100 : 0;
        $strength = min(round(($push + $sit + $core) / 3), 100);

        $hopR = $perf?->triple_hop_right ? ($perf->triple_hop_right / 4.0) * 100 : 0;
        $hopL = $perf?->triple_hop_left ? ($perf->triple_hop_left / 4.0) * 100 : 0;
        $power = min(round(($hopR + $hopL) / 2), 100);

        $agility = $perf?->illinois_agility ? min(round((19.0 / $perf->illinois_agility) * 100), 100) : 0;

        $rastP = $perf?->rast_average_power ? ($perf->rast_average_power / 275) * 100 : 0;
        $rastF = $perf?->rast_fatigue_index ? (5.0 / $perf->rast_fatigue_index) * 100 : 0;
        $beep = $perf?->beep_test_vo2max ? ($perf->beep_test_vo2max / 45.0) * 100 : 0;
        $endurance = min(round(($rastP + $rastF + $beep) / 3), 100);

        return [
            Stat::make('Flexibility', "{$flex}%")->color($flex >= 80 ? 'success' : 'warning'),
            Stat::make('Strength', "{$strength}%")->color($strength >= 80 ? 'success' : 'warning'),
            Stat::make('Power', "{$power}%")->color($power >= 80 ? 'success' : 'warning'),
            Stat::make('Agility', "{$agility}%")->color($agility >= 80 ? 'success' : 'warning'),
            Stat::make('Endurance', "{$endurance}%")->color($endurance >= 80 ? 'success' : 'warning'),
        ];
    }
}