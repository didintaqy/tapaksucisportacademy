@php
    $r = $getRecord();

    // 1. Perhitungan 5 Pilar
    $flex = $r->sit_and_reach ? min(round(($r->sit_and_reach / 28) * 100), 100) : 0;

    $push = $r->push_up_1_min ? ($r->push_up_1_min / 24) * 100 : 0;
    $sit  = $r->sit_up_2_min ? ($r->sit_up_2_min / 45) * 100 : 0;
    $core = $r->core_stability_level ? ($r->core_stability_level / 12) * 100 : 0;
    $strength = min(round(($push + $sit + $core) / 3), 100);

    $hopR = $r->triple_hop_right ? ($r->triple_hop_right / 4.0) * 100 : 0;
    $hopL = $r->triple_hop_left ? ($r->triple_hop_left / 4.0) * 100 : 0;
    $power = min(round(($hopR + $hopL) / 2), 100);

    $agility = $r->illinois_agility ? min(round((19.0 / $r->illinois_agility) * 100), 100) : 0;

    $rastP = $r->rast_average_power ? ($r->rast_average_power / 275) * 100 : 0;
    $rastF = $r->rast_fatigue_index ? (5.0 / $r->rast_fatigue_index) * 100 : 0;
    $beep  = $r->beep_test_vo2max ? ($r->beep_test_vo2max / 45.0) * 100 : 0;
    $endurance = min(round(($rastP + $rastF + $beep) / 3), 100);

    $overall = round(($flex + $strength + $power + $agility + $endurance) / 5);

    // 2. Data Radar Spider Chart
    $radarLabels = [
        'Sit & Reach (Cm)',
        'Push Up 1 Menit',
        'Sit Up 2 Menit',
        'Core Stability 12 Level',
        'Triple Hop Kanan',
        'Triple Hop Kiri',
        'Illinois Agility Test',
        'RAST Average Power',
        'RAST Fatigue Index',
        'Beep Test VO2Max',
    ];

    $radarValues = [
        $r->sit_and_reach ? min(round(($r->sit_and_reach / 28) * 100), 100) : 0,
        $r->push_up_1_min ? min(round(($r->push_up_1_min / 24) * 100), 100) : 0,
        $r->sit_up_2_min ? min(round(($r->sit_up_2_min / 45) * 100), 100) : 0,
        $r->core_stability_level ? min(round(($r->core_stability_level / 12) * 100), 100) : 0,
        $r->triple_hop_right ? min(round(($r->triple_hop_right / 4.0) * 100), 100) : 0,
        $r->triple_hop_left ? min(round(($r->triple_hop_left / 4.0) * 100), 100) : 0,
        $r->illinois_agility ? min(round((19.0 / $r->illinois_agility) * 100), 100) : 0,
        $r->rast_average_power ? min(round(($r->rast_average_power / 275) * 100), 100) : 0,
        $r->rast_fatigue_index ? min(round((5.0 / $r->rast_fatigue_index) * 100), 100) : 0,
        $r->beep_test_vo2max ? min(round(($r->beep_test_vo2max / 45.0) * 100), 100) : 0,
    ];

    $pillars = [
        ['name' => 'Flexibility', 'val' => $flex, 'color' => '#84cc16'],   // Lime Green
        ['name' => 'Strength',    'val' => $strength, 'color' => '#f97316'], // Orange
        ['name' => 'Power',       'val' => $power, 'color' => '#06b6d4'],    // Cyan
        ['name' => 'Agility',     'val' => $agility, 'color' => '#ec4899'],  // Pink
        ['name' => 'Endurance',   'val' => $endurance, 'color' => '#fef08a'], // Pale Yellow
    ];
@endphp

<div class="rounded-2xl bg-[#2e3238] p-6 text-white shadow-md">
    
    {{-- BARIS ATAS: PROFIL FISIK (BESAR) + RADAR CHART --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-12 items-center">
        
        {{-- Donut Besar: PROFIL FISIK ATLET --}}
        <div class="lg:col-span-5 flex flex-col items-center justify-center p-4 border-b lg:border-b-0 lg:border-r border-gray-700/60">
            <h4 class="text-sm font-extrabold tracking-widest text-gray-200 uppercase mb-4">
                PROFIL FISIK ATLET
            </h4>

            <div class="relative flex h-52 w-52 items-center justify-center">
                <svg class="h-full w-full -rotate-90 transform" viewBox="0 0 100 100">
                    <defs>
                        <linearGradient id="gradMain" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" stop-color="#fbbf24" />
                            <stop offset="100%" stop-color="#ec4899" />
                        </linearGradient>
                    </defs>
                    {{-- Background Ring --}}
                    <circle cx="50" cy="50" r="38" stroke="#3f444d" stroke-width="12" fill="none" />
                    {{-- Value Ring (Gradasi Kuning ke Pink sesuai gambar) --}}
                    <circle cx="50" cy="50" r="38" stroke="url(#gradMain)" stroke-width="12" stroke-linecap="round" fill="none"
                        stroke-dasharray="238.76"
                        stroke-dashoffset="{{ 238.76 - (238.76 * ($overall / 100)) }}" />
                </svg>
                <div class="absolute flex flex-col items-center">
                    <span class="text-4xl font-black text-amber-400">{{ $overall }}%</span>
                </div>
            </div>
        </div>

        {{-- DIAGRAM RADAR --}}
        <div class="lg:col-span-7 p-2">
            <h4 class="text-sm font-extrabold tracking-widest text-gray-200 uppercase text-center mb-2">
                DIAGRAM PROSENTASE HASIL TES FISIK
            </h4>

            <div 
                x-data="{
                    init() {
                        const waitChart = setInterval(() => {
                            if (window.Chart) {
                                clearInterval(waitChart);
                                new window.Chart(this.$refs.radar, {
                                    type: 'radar',
                                    data: {
                                        labels: {{ Js::from($radarLabels) }},
                                        datasets: [
                                            {
                                                label: 'Benchmark',
                                                data: Array(10).fill(100),
                                                borderColor: '#3b82f6',
                                                borderWidth: 2,
                                                backgroundColor: 'transparent',
                                                pointBackgroundColor: '#3b82f6',
                                                pointRadius: 2.5,
                                            },
                                            {
                                                label: 'Result',
                                                data: {{ Js::from($radarValues) }},
                                                borderColor: '#eab308',
                                                borderWidth: 2,
                                                backgroundColor: 'rgba(234, 179, 8, 0.15)',
                                                pointBackgroundColor: '#eab308',
                                                pointRadius: 3,
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
                                                grid: { color: 'rgba(255, 255, 255, 0.1)' },
                                                angleLines: { color: 'rgba(255, 255, 255, 0.1)' },
                                                pointLabels: {
                                                    color: '#d1d5db',
                                                    font: { size: 9, weight: '600' }
                                                }
                                            }
                                        },
                                        plugins: {
                                            legend: {
                                                position: 'bottom',
                                                labels: {
                                                    boxWidth: 10,
                                                    color: '#9ca3af',
                                                    font: { size: 10 }
                                                }
                                            }
                                        }
                                    }
                                });
                            }
                        }, 50);
                    }
                }"
                class="relative h-72 w-full"
            >
                <canvas x-ref="radar"></canvas>
            </div>
        </div>
    </div>

    {{-- PEMBATAS HORIZONTAL --}}
    <div class="my-6 border-t border-gray-700"></div>

    {{-- BARIS BAWAH: 5 GAUGE DONUT MINI --}}
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-5 text-center">
        @foreach ($pillars as $p)
            <div class="flex flex-col items-center">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-300 mb-2">
                    {{ $p['name'] }}
                </span>

                <div class="relative flex h-24 w-24 items-center justify-center">
                    <svg class="h-full w-full -rotate-90 transform" viewBox="0 0 100 100">
                        {{-- Background Ring --}}
                        <circle cx="50" cy="50" r="36" stroke="#3f444d" stroke-width="12" fill="none" />
                        {{-- Filled Arc --}}
                        <circle cx="50" cy="50" r="36" stroke="{{ $p['color'] }}" stroke-width="12" stroke-linecap="round" fill="none"
                            stroke-dasharray="226.19"
                            stroke-dashoffset="{{ 226.19 - (226.19 * ($p['val'] / 100)) }}" />
                    </svg>
                    <span class="absolute text-sm font-extrabold text-white">
                        {{ $p['val'] }}%
                    </span>
                </div>
            </div>
        @endforeach
    </div>

</div>