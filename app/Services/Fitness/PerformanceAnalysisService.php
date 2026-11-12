<?php

namespace App\Services\Fitness;

use App\Models\FitnessBenchmark;
use App\Models\FitnessComponent;
use App\Models\FitnessTestItem;
use App\Models\ScoreCategory;
use App\Models\Student;
use App\Models\StudentPerformance;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class PerformanceAnalysisService
{
    /**
     * Default komponen kebugaran sesuai aplikasi REMAJA (Config.gs).
     *
     * @var array<int, array{code: string, name: string, weight: float, sort_order: int}>
     */
    public const DEFAULT_COMPONENTS = [
        ['code' => 'KMP1', 'name' => 'Flexibility', 'weight' => 20.0, 'sort_order' => 1],
        ['code' => 'KMP2', 'name' => 'Strength', 'weight' => 20.0, 'sort_order' => 2],
        ['code' => 'KMP3', 'name' => 'Power', 'weight' => 20.0, 'sort_order' => 3],
        ['code' => 'KMP4', 'name' => 'Agility', 'weight' => 20.0, 'sort_order' => 4],
        ['code' => 'KMP5', 'name' => 'Endurance', 'weight' => 20.0, 'sort_order' => 5],
    ];

    /**
     * Default item tes fisik sesuai aplikasi REMAJA (Config.gs) yang dipetakan ke kolom user_performances.
     *
     * @var array<int, array{code: string, key: string, name: string, component_code: string, unit: string, direction: string, sort_order: int, base_target: float, female_factor: float}>
     */
    public const DEFAULT_ITEMS = [
        [
            'code' => 'ITM1',
            'key' => 'sit_and_reach',
            'name' => 'Sit & Reach',
            'component_code' => 'KMP1',
            'unit' => 'cm',
            'direction' => 'HIGHER',
            'sort_order' => 1,
            'base_target' => 30.0,
            'female_factor' => 1.10,
        ],
        [
            'code' => 'ITM2',
            'key' => 'push_up_1_min',
            'name' => 'Push Up 1 Menit',
            'component_code' => 'KMP2',
            'unit' => 'repetisi',
            'direction' => 'HIGHER',
            'sort_order' => 2,
            'base_target' => 35.0,
            'female_factor' => 0.75,
        ],
        [
            'code' => 'ITM3',
            'key' => 'sit_up_2_min',
            'name' => 'Sit Up 2 Menit',
            'component_code' => 'KMP2',
            'unit' => 'repetisi',
            'direction' => 'HIGHER',
            'sort_order' => 3,
            'base_target' => 50.0,
            'female_factor' => 0.85,
        ],
        [
            'code' => 'ITM4',
            'key' => 'core_stability_level',
            'name' => 'Core Stability',
            'component_code' => 'KMP2',
            'unit' => 'level',
            'direction' => 'HIGHER',
            'sort_order' => 4,
            'base_target' => 12.0,
            'female_factor' => 1.00,
        ],
        [
            'code' => 'ITM5',
            'key' => 'triple_hop_right',
            'name' => 'Triple Hop Kanan',
            'component_code' => 'KMP3',
            'unit' => 'meter',
            'direction' => 'HIGHER',
            'sort_order' => 5,
            'base_target' => 5.5,
            'female_factor' => 0.88,
        ],
        [
            'code' => 'ITM6',
            'key' => 'triple_hop_left',
            'name' => 'Triple Hop Kiri',
            'component_code' => 'KMP3',
            'unit' => 'meter',
            'direction' => 'HIGHER',
            'sort_order' => 6,
            'base_target' => 5.5,
            'female_factor' => 0.88,
        ],
        [
            'code' => 'ITM7',
            'key' => 'illinois_agility',
            'name' => 'Illinois Agility Test',
            'component_code' => 'KMP4',
            'unit' => 'detik',
            'direction' => 'LOWER',
            'sort_order' => 7,
            'base_target' => 16.0,
            'female_factor' => 1.08,
        ],
        [
            'code' => 'ITM8',
            'key' => 'rast_average_power',
            'name' => 'RAST Average Power',
            'component_code' => 'KMP5',
            'unit' => 'watt',
            'direction' => 'HIGHER',
            'sort_order' => 8,
            'base_target' => 450.0,
            'female_factor' => 0.80,
        ],
        [
            'code' => 'ITM9',
            'key' => 'rast_fatigue_index',
            'name' => 'RAST Fatigue Index',
            'component_code' => 'KMP5',
            'unit' => 'watt/detik',
            'direction' => 'LOWER',
            'sort_order' => 9,
            'base_target' => 4.0,
            'female_factor' => 1.10,
        ],
        [
            'code' => 'ITM10',
            'key' => 'beep_test_vo2max',
            'name' => 'Beep Test VO2Max',
            'component_code' => 'KMP5',
            'unit' => 'ml/kg/min',
            'direction' => 'HIGHER',
            'sort_order' => 10,
            'base_target' => 50.0,
            'female_factor' => 0.85,
        ],
    ];

    /**
     * Default kategori skor sesuai aplikasi REMAJA (Config.gs).
     *
     * @var array<int, array{min_score: float, max_score: float, label: string, description: string, color: string}>
     */
    public const DEFAULT_SCORE_CATEGORIES = [
        ['min_score' => 0, 'max_score' => 40, 'label' => 'Kurang Sekali', 'description' => 'Perlu program dasar menyeluruh', 'color' => '#C0392B'],
        ['min_score' => 41, 'max_score' => 55, 'label' => 'Kurang', 'description' => 'Fokus pada komponen terlemah', 'color' => '#E67E22'],
        ['min_score' => 56, 'max_score' => 70, 'label' => 'Cukup', 'description' => 'Pertahankan sambil tingkatkan volume', 'color' => '#D9A31B'],
        ['min_score' => 71, 'max_score' => 85, 'label' => 'Baik', 'description' => 'Siap masuk program spesialisasi', 'color' => '#27924F'],
        ['min_score' => 86, 'max_score' => 100, 'label' => 'Sangat Baik', 'description' => 'Siap dipersiapkan untuk kejuaraan', 'color' => '#186B39'],
    ];

    /**
     * Analisis satu sesi tes fisik terhadap benchmark usia & gender atlet.
     *
     * @return array<string, mixed>
     */
    public function analyze(StudentPerformance $performance, ?Student $student = null): array
    {
        $student ??= $performance->student;
        $referenceDate = $performance->test_date ?? $performance->created_at ?? now();
        $age = $this->calculateAge($student?->birth_date, $referenceDate);
        $gender = $this->normalizeGender($student?->gender);

        $bmi = $this->calculateBmi($performance->height, $performance->weight);
        $bmiCategory = $this->bmiCategory($bmi);

        $heartRate = $this->analyzeHeartRate(
            $performance->resting_heart_rate,
            $performance->max_heart_rate,
            $performance->training_heart_rate,
            $age,
        );

        $components = $this->loadComponents();
        $items = $this->loadTestItems($components);
        $benchmarks = $this->loadBenchmarks();
        $categories = $this->loadScoreCategories();

        $itemResults = [];
        $chartLabels = [];
        $chartScores = [];

        foreach ($items as $item) {
            $target = $this->resolveBenchmarkTarget(
                $benchmarks,
                $item,
                $gender,
                $age ?? 16,
            );

            $rawValue = $this->extractResultValue($performance, $item['key']);
            $numericResult = $rawValue !== null ? (float) $rawValue : 0.0;
            $pct = $this->calculatePercentage($numericResult, $target, $item['direction']);
            $pctCapped = min(100, max(0, $pct));

            $itemResults[] = [
                'code' => $item['code'],
                'key' => $item['key'],
                'name' => $item['name'],
                'component_code' => $item['component_code'],
                'component_name' => $item['component_name'],
                'unit' => $item['unit'],
                'direction' => $item['direction'],
                'inverted' => strtoupper($item['direction']) === 'LOWER',
                'target' => $target,
                'result' => $numericResult,
                'has_value' => $rawValue !== null,
                'raw_pct' => $pct,
                'pct' => $pctCapped,
                'deficit' => max(0, 100 - $pctCapped),
            ];

            $chartLabels[] = $item['name'];
            $chartScores[] = $pctCapped;
        }

        $componentSummaries = [];
        $pillarScores = [];
        $weightedSum = 0.0;
        $totalWeight = 0.0;

        foreach ($components as $component) {
            $compItems = array_values(array_filter(
                $itemResults,
                fn (array $row): bool => $row['component_code'] === $component['code'],
            ));

            $score = count($compItems) > 0
                ? (int) round(array_sum(array_column($compItems, 'pct')) / count($compItems))
                : 0;

            $weight = (float) $component['weight'];
            $weightedSum += $score * $weight;
            $totalWeight += $weight;

            $pillarScores[$component['name']] = $score;
            $componentSummaries[] = [
                'code' => $component['code'],
                'name' => $component['name'],
                'weight' => $weight,
                'score' => $score,
                'deficit' => max(0, 100 - $score),
                'items_count' => count($compItems),
            ];
        }

        $overallScore = $totalWeight > 0
            ? (int) round($weightedSum / $totalWeight)
            : (count($pillarScores) > 0 ? (int) round(array_sum($pillarScores) / count($pillarScores)) : 0);

        $scoreCategory = $this->resolveScoreCategory($categories, $overallScore);

        return [
            'age' => $age,
            'gender' => $gender,
            'test_date' => Carbon::parse($referenceDate),
            'height' => $performance->height !== null ? (float) $performance->height : null,
            'weight' => $performance->weight !== null ? (float) $performance->weight : null,
            'bmi' => $bmi,
            'bmi_category' => $bmiCategory,
            'heart_rate' => $heartRate,
            'items' => $itemResults,
            'components' => $componentSummaries,
            'pillar_scores' => $pillarScores,
            'overall_score' => $overallScore,
            'overall_deficit' => max(0, 100 - $overallScore),
            'score_category' => $scoreCategory,
            'chart_labels' => $chartLabels,
            'chart_scores' => $chartScores,
        ];
    }

    /**
     * Analisis riwayat perkembangan atlet lintas sesi tes fisik.
     *
     * @return array{sessions: array<int, array<string, mixed>>, labels: array<int, string>, overall_series: array<int, int>, component_series: array<string, array<int, int>>}
     */
    public function analyzeProgression(Student $student): array
    {
        $performances = $student->performances()
            ->orderByRaw('COALESCE(test_date, DATE(created_at)) ASC')
            ->orderBy('id')
            ->get();

        $sessions = [];
        $labels = [];
        $overallSeries = [];
        $componentSeries = [];

        foreach ($performances as $performance) {
            $analysis = $this->analyze($performance, $student);
            $label = $analysis['test_date']->format('d/m/Y');

            $sessions[] = [
                'id' => $performance->id,
                'date' => $analysis['test_date']->format('Y-m-d'),
                'label' => $label,
                'overall_score' => $analysis['overall_score'],
                'score_category' => $analysis['score_category'],
                'bmi' => $analysis['bmi'],
                'bmi_category' => $analysis['bmi_category'],
                'pillar_scores' => $analysis['pillar_scores'],
                'analysis' => $analysis,
            ];

            $labels[] = $label;
            $overallSeries[] = $analysis['overall_score'];

            foreach ($analysis['pillar_scores'] as $pillar => $score) {
                $componentSeries[$pillar][] = $score;
            }
        }

        return [
            'sessions' => $sessions,
            'labels' => $labels,
            'overall_series' => $overallSeries,
            'component_series' => $componentSeries,
        ];
    }

    public function calculateAge(mixed $birthDate, mixed $referenceDate = null): ?int
    {
        if (blank($birthDate)) {
            return null;
        }

        $birth = Carbon::parse($birthDate)->startOfDay();
        $ref = $referenceDate ? Carbon::parse($referenceDate)->startOfDay() : now()->startOfDay();

        if ($birth->greaterThan($ref)) {
            return 0;
        }

        return (int) $birth->diffInYears($ref);
    }

    public function calculateBmi(mixed $height, mixed $weight): ?float
    {
        $h = (float) ($height ?? 0);
        $w = (float) ($weight ?? 0);

        if ($h <= 0 || $w <= 0) {
            return null;
        }

        if ($h > 3) {
            $h /= 100;
        }

        return round($w / ($h * $h), 1);
    }

    public function bmiCategory(?float $bmi): string
    {
        if ($bmi === null) {
            return '-';
        }

        return match (true) {
            $bmi < 17.0 => 'KURANG BERAT',
            $bmi < 18.5 => 'KURUS',
            $bmi < 23.0 => 'NORMAL',
            $bmi < 25.0 => 'KELEBIHAN BB',
            default => 'OBESITAS',
        };
    }

    /**
     * @return array{resting: int, max: int, training: int, resting_pct: int, training_pct: int}
     */
    public function analyzeHeartRate(
        mixed $resting,
        mixed $max,
        mixed $training,
        ?int $age = null,
    ): array {
        $rhr = (int) ($resting ?? 0);
        $defaultMax = $age !== null && $age > 0 ? max(120, 220 - $age) : 205;
        $mhr = (int) ($max ?: $defaultMax);
        $thr = (int) ($training ?? 0);

        return [
            'resting' => $rhr,
            'max' => $mhr,
            'training' => $thr,
            'resting_pct' => $mhr > 0 ? (int) round(($rhr / $mhr) * 100) : 0,
            'training_pct' => $mhr > 0 ? (int) round(($thr / $mhr) * 100) : 0,
        ];
    }

    public function calculatePercentage(float $result, float $target, string $direction = 'HIGHER'): int
    {
        if ($target <= 0 || $result <= 0) {
            return 0;
        }

        if (strtoupper($direction) === 'LOWER') {
            return (int) round(($target / $result) * 100);
        }

        return (int) round(($result / $target) * 100);
    }

    private function normalizeGender(?string $gender): string
    {
        $g = strtoupper(trim((string) $gender));

        if (in_array($g, ['P', 'PEREMPUAN', 'F', 'FEMALE', 'WANITA'], true)) {
            return 'P';
        }

        return 'L';
    }

    private function extractResultValue(StudentPerformance $performance, string $key): ?float
    {
        $attributes = $performance->getAttributes();

        if (array_key_exists($key, $attributes) && $attributes[$key] !== null && $attributes[$key] !== '') {
            return (float) $attributes[$key];
        }

        $custom = $performance->custom_results;
        if (is_array($custom) && isset($custom[$key]) && $custom[$key] !== '') {
            return (float) $custom[$key];
        }

        return null;
    }

    /**
     * @return array<int, array{id: ?int, code: string, name: string, weight: float, sort_order: int}>
     */
    private function loadComponents(): array
    {
        try {
            $rows = FitnessComponent::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get();

            if ($rows->isNotEmpty()) {
                return $rows->map(fn (FitnessComponent $c): array => [
                    'id' => $c->id,
                    'code' => $c->code,
                    'name' => $c->name,
                    'weight' => (float) $c->weight,
                    'sort_order' => (int) $c->sort_order,
                ])->all();
            }
        } catch (\Throwable) {
            // Fallback ke default bila tabel belum dimigrasikan
        }

        return array_map(fn (array $c): array => ['id' => null] + $c, self::DEFAULT_COMPONENTS);
    }

    /**
     * @param  array<int, array{id: ?int, code: string, name: string, weight: float, sort_order: int}>  $components
     * @return array<int, array<string, mixed>>
     */
    private function loadTestItems(array $components): array
    {
        $compById = [];
        $compByCode = [];
        foreach ($components as $comp) {
            if ($comp['id'] !== null) {
                $compById[$comp['id']] = $comp;
            }
            $compByCode[$comp['code']] = $comp;
        }

        try {
            $rows = FitnessTestItem::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get();

            if ($rows->isNotEmpty()) {
                return $rows->map(function (FitnessTestItem $item) use ($compById): array {
                    $comp = $compById[$item->fitness_component_id] ?? ['code' => 'KMP1', 'name' => 'General'];

                    return [
                        'id' => $item->id,
                        'code' => $item->code,
                        'key' => $item->key,
                        'name' => $item->name,
                        'component_code' => $comp['code'],
                        'component_name' => $comp['name'],
                        'unit' => (string) ($item->unit ?? ''),
                        'direction' => strtoupper((string) $item->direction),
                        'sort_order' => (int) $item->sort_order,
                    ];
                })->all();
            }
        } catch (\Throwable) {
            // Fallback ke default
        }

        return array_map(function (array $item) use ($compByCode): array {
            $comp = $compByCode[$item['component_code']] ?? ['name' => $item['component_code']];

            return [
                'id' => null,
                'code' => $item['code'],
                'key' => $item['key'],
                'name' => $item['name'],
                'component_code' => $item['component_code'],
                'component_name' => $comp['name'],
                'unit' => $item['unit'],
                'direction' => $item['direction'],
                'sort_order' => $item['sort_order'],
            ];
        }, self::DEFAULT_ITEMS);
    }

    private function loadBenchmarks(): Collection
    {
        try {
            return FitnessBenchmark::query()
                ->where('is_active', true)
                ->get();
        } catch (\Throwable) {
            return collect();
        }
    }

    /**
     * @param  array<string, mixed>  $item
     */
    private function resolveBenchmarkTarget(
        Collection $benchmarks,
        array $item,
        string $gender,
        int $age,
    ): float {
        $clampedAge = max(5, min(40, $age));
        $genderAliases = $gender === 'P'
            ? ['P', 'Perempuan', 'Semua']
            : ['L', 'Laki-laki', 'Semua'];

        if ($item['id'] !== null && $benchmarks->isNotEmpty()) {
            $match = $benchmarks->first(function (FitnessBenchmark $b) use ($item, $genderAliases, $clampedAge): bool {
                return $b->fitness_test_item_id === $item['id']
                    && in_array($b->gender, $genderAliases, true)
                    && $clampedAge >= $b->age_min
                    && $clampedAge <= $b->age_max;
            });

            if (! $match) {
                $match = $benchmarks->first(function (FitnessBenchmark $b) use ($item, $genderAliases): bool {
                    return $b->fitness_test_item_id === $item['id']
                        && in_array($b->gender, $genderAliases, true);
                });
            }

            if ($match) {
                return (float) $match->target_value;
            }
        }

        return $this->computeDefaultBenchmark($item['key'], $item['direction'], $gender, $clampedAge);
    }

    /**
     * Rumus benchmark bawaan REMAJA (Config.gs seedBenchmark_).
     */
    public function computeDefaultBenchmark(string $key, string $direction, string $gender, int $age): float
    {
        $ageFactor = match (true) {
            $age <= 9 => 0.55,
            $age <= 12 => 0.75,
            $age <= 15 => 0.90,
            default => 1.00,
        };

        $defaultItem = collect(self::DEFAULT_ITEMS)->firstWhere('key', $key);
        if (! $defaultItem) {
            return 100.0;
        }

        $value = strtoupper($direction) === 'LOWER'
            ? $defaultItem['base_target'] / $ageFactor
            : $defaultItem['base_target'] * $ageFactor;

        if ($gender === 'P') {
            $value *= $defaultItem['female_factor'];
        }

        if ($key === 'core_stability_level') {
            return (float) min(12, round($value));
        }

        return round($value, 2);
    }

    /**
     * @return array<int, array{min_score: float, max_score: float, label: string, description: string, color: string}>
     */
    private function loadScoreCategories(): array
    {
        try {
            $rows = ScoreCategory::query()->orderBy('min_score')->get();
            if ($rows->isNotEmpty()) {
                return $rows->map(fn (ScoreCategory $c): array => [
                    'min_score' => (float) $c->min_score,
                    'max_score' => (float) $c->max_score,
                    'label' => $c->label,
                    'description' => (string) ($c->description ?? ''),
                    'color' => (string) ($c->color ?: '#27924F'),
                ])->all();
            }
        } catch (\Throwable) {
            // Fallback ke default
        }

        return self::DEFAULT_SCORE_CATEGORIES;
    }

    /**
     * @param  array<int, array{min_score: float, max_score: float, label: string, description: string, color: string}>  $categories
     * @return array{label: string, description: string, color: string}
     */
    public function resolveScoreCategory(array $categories, int|float $score): array
    {
        $selected = null;
        $lowerFallback = null;
        $smallest = null;

        foreach ($categories as $category) {
            if ($score >= $category['min_score'] && $score <= $category['max_score']) {
                $selected = $category;
                break;
            }

            if ($score > $category['max_score'] && ($lowerFallback === null || $category['max_score'] > $lowerFallback['max_score'])) {
                $lowerFallback = $category;
            }

            if ($smallest === null || $category['min_score'] < $smallest['min_score']) {
                $smallest = $category;
            }
        }

        $chosen = $selected ?? $lowerFallback ?? $smallest;

        if (! $chosen) {
            return ['label' => '-', 'description' => '', 'color' => '#888888'];
        }

        return [
            'label' => $chosen['label'],
            'description' => $chosen['description'],
            'color' => $chosen['color'],
        ];
    }
}
