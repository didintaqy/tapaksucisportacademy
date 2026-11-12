<?php

namespace Database\Seeders;

use App\Models\FitnessBenchmark;
use App\Models\FitnessComponent;
use App\Models\FitnessTestItem;
use App\Models\ScoreCategory;
use App\Services\Fitness\PerformanceAnalysisService;
use Illuminate\Database\Seeder;

class FitnessBenchmarkSeeder extends Seeder
{
    /**
     * Seed komponen kebugaran, item tes, kategori skor, dan benchmark usia/gender (REMAJA).
     * Aman dijalankan berulang kali (idempotent).
     */
    public function run(): void
    {
        $componentMap = [];

        foreach (PerformanceAnalysisService::DEFAULT_COMPONENTS as $compData) {
            $component = FitnessComponent::firstOrCreate(
                ['code' => $compData['code']],
                [
                    'name' => $compData['name'],
                    'weight' => $compData['weight'],
                    'sort_order' => $compData['sort_order'],
                    'is_active' => true,
                ],
            );

            $componentMap[$compData['code']] = $component;
        }

        $itemMap = [];

        foreach (PerformanceAnalysisService::DEFAULT_ITEMS as $itemData) {
            $component = $componentMap[$itemData['component_code']];

            $item = FitnessTestItem::firstOrCreate(
                ['key' => $itemData['key']],
                [
                    'code' => $itemData['code'],
                    'name' => $itemData['name'],
                    'fitness_component_id' => $component->id,
                    'unit' => $itemData['unit'],
                    'direction' => $itemData['direction'],
                    'sort_order' => $itemData['sort_order'],
                    'is_active' => true,
                ],
            );

            $itemMap[$itemData['key']] = $item;
        }

        foreach (PerformanceAnalysisService::DEFAULT_SCORE_CATEGORIES as $catData) {
            ScoreCategory::firstOrCreate(
                ['label' => $catData['label']],
                [
                    'min_score' => $catData['min_score'],
                    'max_score' => $catData['max_score'],
                    'description' => $catData['description'],
                    'color' => $catData['color'],
                ],
            );
        }

        $ageBands = [
            [5, 9, 0.55],
            [10, 12, 0.75],
            [13, 15, 0.90],
            [16, 40, 1.00],
        ];

        foreach (['L', 'P'] as $gender) {
            foreach ($ageBands as [$ageMin, $ageMax, $factor]) {
                foreach (PerformanceAnalysisService::DEFAULT_ITEMS as $itemData) {
                    $item = $itemMap[$itemData['key']];
                    $target = $itemData['direction'] === 'LOWER'
                        ? $itemData['base_target'] / $factor
                        : $itemData['base_target'] * $factor;

                    if ($gender === 'P') {
                        $target *= $itemData['female_factor'];
                    }

                    if ($itemData['key'] === 'core_stability_level') {
                        $target = min(12, round($target));
                    } else {
                        $target = round($target, 2);
                    }

                    FitnessBenchmark::firstOrCreate(
                        [
                            'fitness_test_item_id' => $item->id,
                            'gender' => $gender,
                            'age_min' => $ageMin,
                            'age_max' => $ageMax,
                        ],
                        [
                            'target_value' => $target,
                            'is_active' => true,
                        ],
                    );
                }
            }
        }
    }
}
