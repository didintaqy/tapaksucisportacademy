<?php

namespace App\Filament\Resources\StudentPerformances\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Database\Eloquent\Model;

class PhysicalRadarChart extends ChartWidget
{
    protected ?string $heading = 'Diagram Persentase Hasil Tes Fisik';
    protected ?string $maxHeight = '320px';

    public ?Model $record = null;

    protected function getData(): array
    {
        $perf = $this->record;

        $items = [
            'Sit & Reach' => ['target' => 28, 'res' => (float) ($perf?->sit_and_reach ?? 0), 'inv' => false],
            'Push Up' => ['target' => 24, 'res' => (int) ($perf?->push_up_1_min ?? 0), 'inv' => false],
            'Sit Up' => ['target' => 45, 'res' => (int) ($perf?->sit_up_2_min ?? 0), 'inv' => false],
            'Core Stability' => ['target' => 12, 'res' => (int) ($perf?->core_stability_level ?? 0), 'inv' => false],
            'Triple Hop R' => ['target' => 4.0, 'res' => (float) ($perf?->triple_hop_right ?? 0), 'inv' => false],
            'Triple Hop L' => ['target' => 4.0, 'res' => (float) ($perf?->triple_hop_left ?? 0), 'inv' => false],
            'Illinois Agility' => ['target' => 19.0, 'res' => (float) ($perf?->illinois_agility ?? 0), 'inv' => true],
            'RAST Avg' => ['target' => 275, 'res' => (float) ($perf?->rast_average_power ?? 0), 'inv' => false],
            'RAST Fatigue' => ['target' => 5.0, 'res' => (float) ($perf?->rast_fatigue_index ?? 0), 'inv' => true],
            'Beep Test' => ['target' => 45.0, 'res' => (float) ($perf?->beep_test_vo2max ?? 0), 'inv' => false],
        ];

        $labels = [];
        $scores = [];

        foreach ($items as $label => $data) {
            $labels[] = $label;
            if ($data['inv']) {
                $pct = $data['res'] > 0 ? round(($data['target'] / $data['res']) * 100) : 0;
            } else {
                $pct = $data['target'] > 0 ? round(($data['res'] / $data['target']) * 100) : 0;
            }
            $scores[] = min($pct, 100);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Hasil Atlet (%)',
                    'data' => $scores,
                    'borderColor' => '#f59e0b',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.25)',
                    'pointBackgroundColor' => '#f59e0b',
                ],
                [
                    'label' => 'Benchmark (100%)',
                    'data' => array_fill(0, count($labels), 100),
                    'borderColor' => 'rgba(59, 130, 246, 0.5)',
                    'backgroundColor' => 'transparent',
                    'borderDash' => [5, 5],
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'radar';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'r' => [
                    'min' => 0,
                    'max' => 100,
                    'ticks' => ['display' => false],
                ],
            ],
        ];
    }
}