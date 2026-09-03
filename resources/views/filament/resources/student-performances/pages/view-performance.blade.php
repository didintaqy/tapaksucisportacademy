<x-filament-panels::page>
    <div class="space-y-6">

        <style>
            @media print {
                .fi-sidebar, .fi-topbar, .fi-header-actions, .fi-breadcrumbs {
                    display: none !important;
                }
                body, .fi-main {
                    padding: 0 !important;
                    margin: 0 !important;
                    background: white !important;
                }
            }
        </style>

        @php
            $perf = $this->record;
            $student = $perf->student;

            // 1. Antropometri
            $heightM = $perf->height ? (float) $perf->height : null;
            $weightKg = $perf->weight ? (float) $perf->weight : null;
            $bmi = ($heightM && $weightKg && $heightM > 0) ? round($weightKg / ($heightM * $heightM), 1) : null;
            
            $bmiCategory = 'Normal';
            $bmiBadgeColor = 'text-success-600 bg-success-500/10 border-success-500/20';
            if ($bmi) {
                if ($bmi < 18.5) {
                    $bmiCategory = 'Kurus';
                    $bmiBadgeColor = 'text-warning-600 bg-warning-500/10 border-warning-500/20';
                } elseif ($bmi >= 23 && $bmi < 25) {
                    $bmiCategory = 'Kelebihan';
                    $bmiBadgeColor = 'text-warning-600 bg-warning-500/10 border-warning-500/20';
                } elseif ($bmi >= 25) {
                    $bmiCategory = 'OBESITAS';
                    $bmiBadgeColor = 'text-danger-600 bg-danger-500/10 border-danger-500/20';
                }
            }

            // 2. Denyut Nadi
            $rhr = $perf->resting_heart_rate ?? 0;
            $mhr = $perf->max_heart_rate ?? 205;
            $thr = $perf->training_heart_rate ?? 0;

            $rhrPercent = $mhr > 0 ? round(($rhr / $mhr) * 100) : 0;
            $thrPercent = $mhr > 0 ? round(($thr / $mhr) * 100) : 0;

            // 3. Benchmark Data
            $tests = [
                'Sit & Reach (Cm)' => [
                    'category' => 'Flexibility',
                    'target' => 28,
                    'result' => (float) ($perf->sit_and_reach ?? 0),
                    'unit' => 'cm',
                    'inverted' => false,
                ],
                'Push Up 1 Menit' => [
                    'category' => 'Strength',
                    'target' => 24,
                    'result' => (int) ($perf->push_up_1_min ?? 0),
                    'unit' => 'rep',
                    'inverted' => false,
                ],
                'Sit Up 2 Menit' => [
                    'category' => 'Strength',
                    'target' => 45,
                    'result' => (int) ($perf->sit_up_2_min ?? 0),
                    'unit' => 'rep',
                    'inverted' => false,
                ],
                'Core Stability 12 Level' => [
                    'category' => 'Strength',
                    'target' => 12,
                    'result' => (int) ($perf->core_stability_level ?? 0),
                    'unit' => 'lvl',
                    'inverted' => false,
                ],
                'Triple Hop Kanan' => [
                    'category' => 'Power',
                    'target' => 4.0,
                    'result' => (float) ($perf->triple_hop_right ?? 0),
                    'unit' => 'm',
                    'inverted' => false,
                ],
                'Triple Hop Kiri' => [
                    'category' => 'Power',
                    'target' => 4.0,
                    'result' => (float) ($perf->triple_hop_left ?? 0),
                    'unit' => 'm',
                    'inverted' => false,
                ],
                'Illinois Agility Test' => [
                    'category' => 'Agility',
                    'target' => 19.0,
                    'result' => (float) ($perf->illinois_agility ?? 0),
                    'unit' => 'dtk',
                    'inverted' => true,
                ],
                'RAST - Average Power' => [
                    'category' => 'Endurance',
                    'target' => 275,
                    'result' => (float) ($perf->rast_average_power ?? 0),
                    'unit' => 'W',
                    'inverted' => false,
                ],
                'RAST - Fatigue Index' => [
                    'category' => 'Endurance',
                    'target' => 5.0,
                    'result' => (float) ($perf->rast_fatigue_index ?? 0),
                    'unit' => 'W',
                    'inverted' => true,
                ],
                'Beep Test VO2Max' => [
                    'category' => 'Endurance',
                    'target' => 45.0,
                    'result' => (float) ($perf->beep_test_vo2max ?? 0),
                    'unit' => 'ml/kg/min',
                    'inverted' => false,
                ],
            ];

            $chartLabels = [];
            $chartScores = [];
            $categoryScores = [];

            foreach ($tests as $key => &$item) {
                $chartLabels[] = $key;
                if ($item['inverted']) {
                    $pct = $item['result'] > 0 ? round(($item['target'] / $item['result']) * 100) : 0;
                } else {
                    $pct = $item['target'] > 0 ? round(($item['result'] / $item['target']) * 100) : 0;
                }
                $pctCapped = min($pct, 100);
                $item['pct'] = $pct;
                $chartScores[] = $pctCapped;
                $categoryScores[$item['category']][] = $pctCapped;
            }
            unset($item);

            $pillarScores = [
                'Flexibility' => isset($categoryScores['Flexibility']) ? round(array_sum($categoryScores['Flexibility']) / count($categoryScores['Flexibility'])) : 0,
                'Strength' => isset($categoryScores['Strength']) ? round(array_sum($categoryScores['Strength']) / count($categoryScores['Strength'])) : 0,
                'Power' => isset($categoryScores['Power']) ? round(array_sum($categoryScores['Power']) / count($categoryScores['Power'])) : 0,
                'Agility' => isset($categoryScores['Agility']) ? round(array_sum($categoryScores['Agility']) / count($categoryScores['Agility'])) : 0,
                'Endurance' => isset($categoryScores['Endurance']) ? round(array_sum($categoryScores['Endurance']) / count($categoryScores['Endurance'])) : 0,
            ];

            $overallScore = round(array_sum($pillarScores) / 5);
            $studentAge = $student?->birth_date ? \Carbon\Carbon::parse($student->birth_date)->age : '-';
        @endphp

        {{-- HEADER & BIODATA SISWA --}}
        <div class="relative overflow-hidden rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-gray-900">
            <div class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
                <div class="flex items-center gap-5">
                    <div class="relative h-24 w-24 shrink-0 overflow-hidden rounded-2xl border-2 border-primary-500 shadow-md">
                        @if ($student?->photo)
                            <img src="{{ Storage::url($student->photo) }}" alt="{{ $student->full_name }}" class="h-full w-full object-cover">
                        @else
                            <div class="flex h-full w-full items-center justify-center bg-gray-100 dark:bg-gray-800 text-gray-400">
                                <x-heroicon-o-user class="h-12 w-12" />
                            </div>
                        @endif
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center rounded-md bg-primary-50 px-2 py-0.5 text-xs font-semibold text-primary-700 ring-1 ring-inset ring-primary-700/10 dark:bg-primary-500/10 dark:text-primary-400">
                                TAPAK SUCI SPORT ACADEMY
                            </span>
                            <span class="text-xs text-gray-500">ID: AT-{{ str_pad($student?->id ?? 0, 4, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <h1 class="mt-1 text-2xl font-black tracking-tight text-gray-950 dark:text-white uppercase">
                            {{ $student?->full_name ?? 'NAMA SISWA' }}
                        </h1>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            {{ $student?->birth_place ?? 'BATAM' }}, {{ $student?->birth_date ? \Carbon\Carbon::parse($student->birth_date)->translatedFormat('d F Y') : '-' }} ({{ $studentAge }} Tahun) • {{ $student?->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3 border-t pt-4 md:border-t-0 md:pt-0 sm:flex">
                    <div class="rounded-xl border border-gray-100 bg-gray-50/50 p-3 text-center dark:border-white/5 dark:bg-white/5">
                        <span class="text-xs text-gray-400 uppercase font-semibold">Tingkat Sabuk</span>
                        <p class="text-sm font-bold text-amber-600 dark:text-amber-400">{{ $student?->belt_level ?? '-' }}</p>
                    </div>
                    <div class="rounded-xl border border-gray-100 bg-gray-50/50 p-3 text-center dark:border-white/5 dark:bg-white/5">
                        <span class="text-xs text-gray-400 uppercase font-semibold">Pendidikan</span>
                        <p class="text-sm font-bold text-primary-600 dark:text-primary-400">{{ $student?->education ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- METRIK ANTROPOMETRI & SKRINING NADI --}}
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-gray-900">
                <div class="flex items-center gap-2 mb-4">
                    <x-heroicon-m-scale class="h-5 w-5 text-primary-500" />
                    <h3 class="font-bold text-base text-gray-900 dark:text-white">B. Tes Antropometri</h3>
                </div>
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                    <div class="rounded-xl bg-gray-50 p-3 dark:bg-white/5">
                        <div class="text-xs text-gray-500">Tinggi Badan</div>
                        <div class="mt-1 text-lg font-bold text-gray-900 dark:text-white">{{ $perf->height ?? '-' }} <span class="text-xs font-normal">m</span></div>
                    </div>
                    <div class="rounded-xl bg-gray-50 p-3 dark:bg-white/5">
                        <div class="text-xs text-gray-500">Berat Badan</div>
                        <div class="mt-1 text-lg font-bold text-gray-900 dark:text-white">{{ $perf->weight ?? '-' }} <span class="text-xs font-normal">kg</span></div>
                    </div>
                    <div class="rounded-xl bg-gray-50 p-3 dark:bg-white/5">
                        <div class="text-xs text-gray-500">Body Mass Index</div>
                        <div class="mt-1 text-lg font-bold text-gray-900 dark:text-white">{{ $bmi ?? '-' }}</div>
                    </div>
                    <div class="rounded-xl border p-3 flex flex-col justify-center items-center {{ $bmiBadgeColor }}">
                        <span class="text-[10px] font-bold uppercase tracking-wider">Kategori</span>
                        <span class="text-xs font-extrabold uppercase">{{ $bmiCategory }}</span>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-gray-900">
                <div class="flex items-center gap-2 mb-4">
                    <x-heroicon-m-heart class="h-5 w-5 text-danger-500" />
                    <h3 class="font-bold text-base text-gray-900 dark:text-white">C. Skrining Denyut Nadi</h3>
                </div>
                <div class="grid grid-cols-3 gap-3">
                    <div class="rounded-xl bg-gray-50 p-3 text-center dark:bg-white/5">
                        <div class="text-xs text-gray-500">Nadi Istirahat</div>
                        <div class="mt-1 text-base font-bold text-gray-900 dark:text-white">{{ $rhr }} <span class="text-[10px] font-normal text-gray-400">/mnt</span></div>
                        <span class="mt-1 inline-block text-xs font-semibold text-primary-500">{{ $rhrPercent }}%</span>
                    </div>
                    <div class="rounded-xl bg-gray-50 p-3 text-center dark:bg-white/5">
                        <div class="text-xs text-gray-500">Nadi Maksimal</div>
                        <div class="mt-1 text-base font-bold text-gray-900 dark:text-white">{{ $mhr }} <span class="text-[10px] font-normal text-gray-400">/mnt</span></div>
                        <span class="mt-1 inline-block text-xs font-semibold text-primary-500">100%</span>
                    </div>
                    <div class="rounded-xl bg-gray-50 p-3 text-center dark:bg-white/5">
                        <div class="text-xs text-gray-500">Nadi Latihan</div>
                        <div class="mt-1 text-base font-bold text-gray-900 dark:text-white">{{ $thr }} <span class="text-[10px] font-normal text-gray-400">/mnt</span></div>
                        <span class="mt-1 inline-block text-xs font-semibold text-primary-500">{{ $thrPercent }}%</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- VISUAL SUMMARY: RADIAL + RADAR --}}
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="flex flex-col items-center justify-center rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-gray-900">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Profil Fisik Atlet</span>
                <div class="relative my-4 flex h-36 w-36 items-center justify-center">
                    <svg class="h-full w-full -rotate-90 transform" viewBox="0 0 36 36">
                        <path class="text-gray-100 dark:text-gray-800" stroke-width="3.5" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                        <path class="text-amber-500 transition-all duration-1000 ease-out" stroke-dasharray="{{ $overallScore }}, 100" stroke-width="3.5" stroke-linecap="round" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                    </svg>
                    <div class="absolute flex flex-col items-center">
                        <span class="text-3xl font-black text-gray-900 dark:text-white">{{ $overallScore }}%</span>
                        <span class="text-[10px] font-medium text-gray-400">Rata-rata</span>
                    </div>
                </div>
                <p class="text-center text-xs text-gray-500">Skor agregat 5 komponen kebugaran silat</p>
            </div>

            <div class="col-span-1 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-gray-900 lg:col-span-2">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Diagram Persentase Hasil Tes Fisik</span>
                <div 
                    x-data="{
                        chart: null,
                        init() {
                            const check = setInterval(() => {
                                if (window.Chart) {
                                    clearInterval(check);
                                    this.renderChart();
                                }
                            }, 50);
                        },
                        renderChart() {
                            if (this.chart) this.chart.destroy();
                            this.chart = new window.Chart(this.$refs.canvas, {
                                type: 'radar',
                                data: {
                                    labels: {{ Js::from($chartLabels) }},
                                    datasets: [
                                        {
                                            label: 'Benchmark (100%)',
                                            data: Array({{ count($chartLabels) }}).fill(100),
                                            borderColor: 'rgba(59, 130, 246, 0.6)',
                                            backgroundColor: 'transparent',
                                            borderDash: [5, 5],
                                            pointRadius: 2,
                                        },
                                        {
                                            label: 'Hasil Atlet (%)',
                                            data: {{ Js::from($chartScores) }},
                                            borderColor: '#f59e0b',
                                            backgroundColor: 'rgba(245, 158, 11, 0.25)',
                                            pointBackgroundColor: '#f59e0b',
                                            pointBorderColor: '#fff',
                                            pointRadius: 4,
                                        }
                                    ]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    scales: {
                                        r: {
                                            min: 0,
                                            max: 100,
                                            ticks: { display: false, stepSize: 20 },
                                            grid: { color: 'rgba(156, 163, 175, 0.2)' },
                                            pointLabels: {
                                                font: { size: 10, weight: '600' },
                                                color: '#6b7280'
                                            }
                                        }
                                    },
                                    plugins: {
                                        legend: { 
                                            position: 'bottom',
                                            labels: { boxWidth: 12, font: { size: 11 } }
                                        }
                                    }
                                }
                            });
                        }
                    }"
                    class="relative mt-2 h-72 w-full"
                >
                    <canvas x-ref="canvas"></canvas>
                </div>
            </div>
        </div>

        {{-- 5 PILAR FISIK --}}
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-5">
            @foreach ($pillarScores as $pillar => $score)
                <div class="rounded-xl border border-gray-200 bg-white p-4 text-center shadow-sm dark:border-white/10 dark:bg-gray-900">
                    <span class="text-xs font-semibold text-gray-500 uppercase">{{ $pillar }}</span>
                    <div class="mt-2 text-2xl font-extrabold text-gray-900 dark:text-white">{{ $score }}%</div>
                    <div class="mt-2 w-full bg-gray-100 rounded-full h-1.5 dark:bg-gray-800 overflow-hidden">
                        <div class="bg-primary-500 h-1.5 rounded-full" style="width: {{ min($score, 100) }}%"></div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- TABEL RINCIAN TES FISIK LENGKAP --}}
        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-white/10 dark:bg-gray-900">
            <div class="border-b border-gray-200 px-6 py-4 dark:border-white/10 flex items-center justify-between">
                <h3 class="font-bold text-base text-gray-900 dark:text-white">D. Rincian Uji Komponen Fisik</h3>
                <span class="text-xs text-gray-400">Satuan terstandar TSSA</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-600 dark:text-gray-300">
                    <thead class="bg-gray-50 text-xs uppercase font-semibold text-gray-500 dark:bg-white/5 dark:text-gray-400">
                        <tr>
                            <th class="px-6 py-3.5">Komponen Fisik</th>
                            <th class="px-6 py-3.5">Item Tes</th>
                            <th class="px-6 py-3.5 text-center">Benchmark</th>
                            <th class="px-6 py-3.5 text-center">% Target</th>
                            <th class="px-6 py-3.5 text-center">Hasil Tes</th>
                            <th class="px-6 py-3.5 text-center">% Capaian</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                        @foreach ($tests as $name => $data)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-white/[0.02] transition">
                                <td class="px-6 py-4 font-semibold text-gray-900 dark:text-white">
                                    <span class="inline-flex items-center rounded-md bg-gray-100 px-2 py-0.5 text-xs text-gray-700 dark:bg-white/10 dark:text-gray-300">
                                        {{ $data['category'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 font-medium">{{ $name }}</td>
                                <td class="px-6 py-4 text-center font-mono">{{ $data['target'] }} {{ $data['unit'] }}</td>
                                <td class="px-6 py-4 text-center font-semibold text-primary-600 dark:text-primary-400">100%</td>
                                <td class="px-6 py-4 text-center font-mono font-bold text-gray-950 dark:text-white">
                                    {{ $data['result'] }} {{ $data['unit'] }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span @class([
                                        'inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold',
                                        'bg-success-50 text-success-700 dark:bg-success-500/10 dark:text-success-400' => $data['pct'] >= 100,
                                        'bg-primary-50 text-primary-700 dark:bg-primary-500/10 dark:text-primary-400' => $data['pct'] >= 80 && $data['pct'] < 100,
                                        'bg-danger-50 text-danger-700 dark:bg-danger-500/10 dark:text-danger-400' => $data['pct'] < 80,
                                    ])>
                                        {{ $data['pct'] }}%
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- FOOTER TIM PELATIH --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-gray-900">
            <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Tim Pelatih & Penguji</span>
            <div class="mt-6 grid grid-cols-1 gap-6 sm:grid-cols-3 text-center">
                <div class="space-y-1">
                    <div class="mx-auto h-12 w-32 border-b border-gray-300 dark:border-gray-700"></div>
                    <p class="font-bold text-sm text-gray-900 dark:text-white pt-2">HENNY WIJAYANTI</p>
                    <p class="text-xs text-gray-400">Pelatih Fisik</p>
                </div>
                <div class="space-y-1">
                    <div class="mx-auto h-12 w-32 border-b border-gray-300 dark:border-gray-700"></div>
                    <p class="font-bold text-sm text-gray-900 dark:text-white pt-2">WULAN RAMADANI</p>
                    <p class="text-xs text-gray-400">Pelatih Teknik</p>
                </div>
                <div class="space-y-1">
                    <div class="mx-auto h-12 w-32 border-b border-gray-300 dark:border-gray-700"></div>
                    <p class="font-bold text-sm text-gray-900 dark:text-white pt-2">M. TAQIYYUDIN ROHANAN</p>
                    <p class="text-xs text-gray-400">Head Coach</p>
                </div>
            </div>
        </div>

    </div>
</x-filament-panels::page>