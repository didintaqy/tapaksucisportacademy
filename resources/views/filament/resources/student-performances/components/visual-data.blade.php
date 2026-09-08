@php
    $r = $getRecord();
    $student = $r->student ?? null;

    // --- 1. DATA ANTROPOMETRI & BMI ---
    $height = (float) ($r->height ?? 0);
    $weight = (float) ($r->weight ?? 0);
    $bmi = ($height > 0 && $weight > 0) ? round($weight / ($height * $height), 1) : '-';
    $kategoriBmi = match(true) {
        $bmi === '-' => '-',
        $bmi < 18.5  => 'KURUS',
        $bmi < 23.0  => 'NORMAL',
        $bmi < 25.0  => 'KELEBIHAN BB',
        default      => 'OBESITAS',
    };

    // --- 2. DATA TES FISIK & PERSENTASE ---
    $valSitReach  = (float) ($r->sit_and_reach ?? 0);
    $valPushUp    = (float) ($r->push_up_1_min ?? 0);
    $valSitUp     = (float) ($r->sit_up_2_min ?? 0);
    $valCore      = (float) ($r->core_stability_level ?? 0);
    $valHopR      = (float) ($r->triple_hop_right ?? 0);
    $valHopL      = (float) ($r->triple_hop_left ?? 0);
    $valIllinois  = (float) ($r->illinois_agility ?? 0);
    $valRastP     = (float) ($r->rast_average_power ?? 0);
    $valRastF     = (float) ($r->rast_fatigue_index ?? 0);
    $valBeep      = (float) ($r->beep_test_vo2max ?? 0);

    $pctSitReach  = min(round(($valSitReach / 28) * 100), 100);
    $pctPushUp    = min(round(($valPushUp / 24) * 100), 100);
    $pctSitUp     = min(round(($valSitUp / 45) * 100), 100);
    $pctCore      = min(round(($valCore / 12) * 100), 100);
    $pctHopR      = min(round(($valHopR / 4.0) * 100), 100);
    $pctHopL      = min(round(($valHopL / 4.0) * 100), 100);
    $pctIllinois  = $valIllinois > 0 ? min(round((19.0 / $valIllinois) * 100), 100) : 0;
    $pctRastP     = min(round(($valRastP / 275) * 100), 100);
    $pctRastF     = $valRastF > 0 ? min(round((5.0 / $valRastF) * 100), 100) : 0;
    $pctBeep      = min(round(($valBeep / 45.0) * 100), 100);

    // --- 3. PERHITUNGAN 5 PILAR ---
    $flex      = $pctSitReach;
    $strength  = min(round(($pctPushUp + $pctSitUp + $pctCore) / 3), 100);
    $power     = min(round(($pctHopR + $pctHopL) / 2), 100);
    $agility   = $pctIllinois;
    $endurance = min(round(($pctRastP + $pctRastF + $pctBeep) / 3), 100);
    $overall   = round(($flex + $strength + $power + $agility + $endurance) / 5);

    $pillars = [
        ['name' => 'Flexibility', 'val' => $flex,      'color' => '#84cc16'],
        ['name' => 'Strength',    'val' => $strength,  'color' => '#f97316'],
        ['name' => 'Power',       'val' => $power,     'color' => '#06b6d4'],
        ['name' => 'Agility',     'val' => $agility,   'color' => '#ec4899'],
        ['name' => 'Endurance',   'val' => $endurance, 'color' => '#fef08a'],
    ];

    $radarLabels = [
        'Sit & Reach (Cm)', 'Push Up 1 Menit', 'Sit Up 2 Menit', 'Core Stability 12 Level',
        'Triple Hop Kanan', 'Triple Hop Kiri', 'Illinois Agility Test',
        'RAST Average Power', 'RAST Fatigue Index', 'Beep Test VO2Max',
    ];
    $radarValues = [$pctSitReach, $pctPushUp, $pctSitUp, $pctCore, $pctHopR, $pctHopL, $pctIllinois, $pctRastP, $pctRastF, $pctBeep];
@endphp

<div style="background-color: #ffffff; color: #333333; font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; padding: 24px; border-radius: 8px; border: 1px solid #e5e7eb; max-width: 950px; margin: 0 auto; box-sizing: border-box;">

    <!-- ==================== A. BIODATA ATLET ==================== -->
    <div style="margin-bottom: 20px;">
        <div style="background-color: #00adef; color: #ffffff; font-weight: 700; padding: 4px 12px; font-size: 13px; letter-spacing: 0.5px;">
            A. BIODATA ATLET
        </div>
        <div style="display: flex; gap: 20px; padding: 12px 6px; align-items: flex-start; flex-wrap: wrap;">
            <div style="width: 110px; height: 140px; background-color: #f3f4f6; border: 1px solid #d1d5db; overflow: hidden; flex-shrink: 0;">
                @if($student && $student->photo)
                    <img src="{{ asset('storage/' . $student->photo) }}" style="width: 100%; height: 100%; object-fit: cover;">
                @else
                    <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; font-size: 11px; color: #9ca3af; text-align: center; padding: 4px;">
                        Foto Atlet
                    </div>
                @endif
            </div>
            
            <table style="font-size: 12px; border-collapse: collapse; flex: 1; min-width: 250px;">
                <tr style="height: 22px;"><td style="width: 190px; color: #374151;">Id Atlet</td><td style="width: 15px;">:</td><td style="font-weight: bold; color: #111827;">{{ $student->id ?? 'ATL1' }}</td></tr>
                <tr style="height: 22px;"><td style="color: #374151;">Nama Lengkap</td><td>:</td><td style="font-weight: bold; color: #111827; text-transform: uppercase;">{{ $student->full_name ?? 'BARRA MAHAMERU ARRILY' }}</td></tr>
                <tr style="height: 22px;"><td style="color: #374151;">Tempat, Tanggal Lahir</td><td>:</td><td>{{ ($student->birth_place ?? 'Batam') . ', ' . ($student && $student->birth_date ? \Carbon\Carbon::parse($student->birth_date)->translatedFormat('d F Y') : '14 Mei 2011') }}</td></tr>
                <tr style="height: 22px;"><td style="color: #374151;">Usia</td><td>:</td><td>{{ $student && $student->birth_date ? \Carbon\Carbon::parse($student->birth_date)->age : '15' }}</td></tr>
                <tr style="height: 22px;"><td style="color: #374151;">Jenis Kelamin</td><td>:</td><td>{{ ($student->gender ?? 'L') === 'L' ? 'Laki-laki' : 'Perempuan' }}</td></tr>
                <tr style="height: 22px;"><td style="color: #374151;">Kelas / Nomor Pertandingan</td><td>:</td><td style="font-weight: 600;">{{ $student->belt_level ?? 'FIGHT' }}</td></tr>
                <tr style="height: 22px;"><td style="color: #374151;">Asal Daerah</td><td>:</td><td style="font-weight: 600;">{{ $student->address ?? 'BATAM, KEPRI' }}</td></tr>
            </table>
        </div>
    </div>

    <!-- ==================== B & C (2 KOLOM HORIZONTAL) ==================== -->
    <div style="display: flex; gap: 20px; margin-bottom: 20px; flex-wrap: wrap;">
        <!-- B. ANTROPOMETRI -->
        <div style="flex: 1; min-width: 300px;">
            <div style="background-color: #00adef; color: #ffffff; font-weight: 700; padding: 4px 12px; font-size: 13px;">
                B. TES ANTROPOMETRI
            </div>
            <table style="width: 100%; font-size: 12px; margin-top: 10px; border-collapse: collapse;">
                <tr style="height: 24px;"><td style="width: 180px; color: #374151;">Tinggi Badan (m)</td><td style="width: 15px;">:</td><td>{{ $r->height ?? '1.55' }}</td></tr>
                <tr style="height: 24px;"><td style="color: #374151;">Berat Badan (kg)</td><td>:</td><td>{{ $r->weight ?? '69' }}</td></tr>
                <tr style="height: 24px;"><td style="color: #374151;">Body Mass Index (BMI)</td><td>:</td><td style="font-weight: 600;">{{ $bmi }}</td></tr>
                <tr style="height: 24px;"><td style="color: #374151;">Kategori BMI</td><td>:</td><td style="font-weight: bold; color: #dc2626;">{{ $kategoriBmi }}</td></tr>
            </table>
        </div>

        <!-- C. SKRINING DENYUT NADI -->
        <div style="flex: 1; min-width: 300px;">
            <div style="background-color: #00adef; color: #ffffff; font-weight: 700; padding: 4px 12px; font-size: 13px;">
                C. SKRINING DENYUT NADI
            </div>
            <table style="width: 100%; font-size: 12px; margin-top: 10px; border-collapse: collapse;">
                <tr style="height: 28px;">
                    <td style="width: 140px; color: #374151;">Denyut Nadi Istirahat</td>
                    <td style="width: 60px;">: {{ $r->resting_heart_rate ?? '110' }}</td>
                    <td style="color: #9ca3af; width: 55px;">/ menit</td>
                    <td>
                        <div style="width: 100px; height: 16px; background: #e5e7eb; border: 1px solid #10b981; border-radius: 2px; overflow: hidden;">
                            <div style="width: 54%; height: 100%; background: #10b981; color: #fff; font-size: 10px; font-weight: bold; text-align: center; line-height: 16px;">54%</div>
                        </div>
                    </td>
                </tr>
                <tr style="height: 28px;">
                    <td style="color: #374151;">Denyut Nadi Maksimal</td>
                    <td>: {{ $r->max_heart_rate ?? '205' }}</td>
                    <td style="color: #9ca3af;">/ menit</td>
                    <td>
                        <div style="width: 100px; height: 16px; background: #e5e7eb; border: 1px solid #10b981; border-radius: 2px; overflow: hidden;">
                            <div style="width: 100%; height: 100%; background: #10b981; color: #fff; font-size: 10px; font-weight: bold; text-align: center; line-height: 16px;">100%</div>
                        </div>
                    </td>
                </tr>
                <tr style="height: 28px;">
                    <td style="color: #374151;">Denyut Nadi Latihan</td>
                    <td>: {{ $r->training_heart_rate ?? '170' }}</td>
                    <td style="color: #9ca3af;">/ menit</td>
                    <td>
                        <div style="width: 100px; height: 16px; background: #e5e7eb; border: 1px solid #10b981; border-radius: 2px; overflow: hidden;">
                            <div style="width: 83%; height: 100%; background: #10b981; color: #fff; font-size: 10px; font-weight: bold; text-align: center; line-height: 16px;">83%</div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <!-- ==================== D. TES FISIK ==================== -->
    <div style="margin-bottom: 20px;">
        <div style="background-color: #00adef; color: #ffffff; font-weight: 700; padding: 4px 12px; font-size: 13px;">
            D. TES FISIK
        </div>
        <table style="width: 100%; font-size: 11px; border-collapse: collapse; border: 1px solid #cbd5e1; margin-top: 8px;">
            <thead>
                <tr style="background-color: #262626; color: #ffffff; text-align: center; height: 26px;">
                    <th style="border: 1px solid #525252; width: 100px; padding: 4px;">Komponen Fisik</th>
                    <th style="border: 1px solid #525252; padding: 4px;">Item Tes</th>
                    <th style="border: 1px solid #525252; width: 75px; padding: 4px;">Benchmark</th>
                    <th style="border: 1px solid #525252; width: 110px; padding: 4px;">% Benchmark</th>
                    <th style="border: 1px solid #525252; width: 75px; padding: 4px;">Hasil Test</th>
                    <th style="border: 1px solid #525252; width: 130px; padding: 4px;">% Hasil Test</th>
                </tr>
            </thead>
            <tbody>
                <!-- Flexibility -->
                <tr>
                    <td style="background-color: #0284c7; color: #fff; font-weight: bold; text-align: center; border: 1px solid #cbd5e1; padding: 5px;">Flexibility</td>
                    <td style="border: 1px solid #cbd5e1; padding: 5px;">Sit & Reach (Cm)</td>
                    <td style="text-align: center; border: 1px solid #cbd5e1;">28</td>
                    <td style="border: 1px solid #cbd5e1; padding: 4px;">
                        <div style="background: #f1f5f9; border: 1px solid #f59e0b; height: 14px;"><div style="width: 100%; height: 100%; background: #fbbf24; font-size: 9px; font-weight: bold; text-align: center; line-height: 14px;">100%</div></div>
                    </td>
                    <td style="text-align: center; font-weight: bold; border: 1px solid #cbd5e1;">{{ $valSitReach }}</td>
                    <td style="border: 1px solid #cbd5e1; padding: 4px;">
                        <div style="background: #f1f5f9; border: 1px solid #0284c7; height: 14px;"><div style="width: {{ $pctSitReach }}%; height: 100%; background: #38bdf8; font-size: 9px; font-weight: bold; text-align: center; line-height: 14px; color: #fff;">{{ $pctSitReach }}%</div></div>
                    </td>
                </tr>
                <!-- Strength -->
                <tr>
                    <td rowspan="3" style="background-color: #0369a1; color: #fff; font-weight: bold; text-align: center; border: 1px solid #cbd5e1; padding: 5px;">Strength</td>
                    <td style="border: 1px solid #cbd5e1; padding: 5px;">Push Up 1 Menit (Repetisi)</td>
                    <td style="text-align: center; border: 1px solid #cbd5e1;">24</td>
                    <td style="border: 1px solid #cbd5e1; padding: 4px;"><div style="background: #f1f5f9; border: 1px solid #f59e0b; height: 14px;"><div style="width: 100%; height: 100%; background: #fbbf24; font-size: 9px; font-weight: bold; text-align: center; line-height: 14px;">100%</div></div></td>
                    <td style="text-align: center; font-weight: bold; border: 1px solid #cbd5e1;">{{ $valPushUp }}</td>
                    <td style="border: 1px solid #cbd5e1; padding: 4px;"><div style="background: #f1f5f9; border: 1px solid #0284c7; height: 14px;"><div style="width: {{ $pctPushUp }}%; height: 100%; background: #38bdf8; font-size: 9px; font-weight: bold; text-align: center; line-height: 14px; color: #fff;">{{ $pctPushUp }}%</div></div></td>
                </tr>
                <tr>
                    <td style="border: 1px solid #cbd5e1; padding: 5px;">Sit Up 2 Menit (Repetisi)</td>
                    <td style="text-align: center; border: 1px solid #cbd5e1;">45</td>
                    <td style="border: 1px solid #cbd5e1; padding: 4px;"><div style="background: #f1f5f9; border: 1px solid #f59e0b; height: 14px;"><div style="width: 100%; height: 100%; background: #fbbf24; font-size: 9px; font-weight: bold; text-align: center; line-height: 14px;">100%</div></div></td>
                    <td style="text-align: center; font-weight: bold; border: 1px solid #cbd5e1;">{{ $valSitUp }}</td>
                    <td style="border: 1px solid #cbd5e1; padding: 4px;"><div style="background: #f1f5f9; border: 1px solid #0284c7; height: 14px;"><div style="width: {{ $pctSitUp }}%; height: 100%; background: #38bdf8; font-size: 9px; font-weight: bold; text-align: center; line-height: 14px; color: #fff;">{{ $pctSitUp }}%</div></div></td>
                </tr>
                <tr>
                    <td style="border: 1px solid #cbd5e1; padding: 5px;">Core Stability 12 Level (Level)</td>
                    <td style="text-align: center; border: 1px solid #cbd5e1;">12</td>
                    <td style="border: 1px solid #cbd5e1; padding: 4px;"><div style="background: #f1f5f9; border: 1px solid #f59e0b; height: 14px;"><div style="width: 100%; height: 100%; background: #fbbf24; font-size: 9px; font-weight: bold; text-align: center; line-height: 14px;">100%</div></div></td>
                    <td style="text-align: center; font-weight: bold; border: 1px solid #cbd5e1;">{{ $valCore }}</td>
                    <td style="border: 1px solid #cbd5e1; padding: 4px;"><div style="background: #f1f5f9; border: 1px solid #0284c7; height: 14px;"><div style="width: {{ $pctCore }}%; height: 100%; background: #38bdf8; font-size: 9px; font-weight: bold; text-align: center; line-height: 14px; color: #fff;">{{ $pctCore }}%</div></div></td>
                </tr>
                <!-- Power -->
                <tr>
                    <td rowspan="2" style="background-color: #0284c7; color: #fff; font-weight: bold; text-align: center; border: 1px solid #cbd5e1; padding: 5px;">Power</td>
                    <td style="border: 1px solid #cbd5e1; padding: 5px;">Triple Hop Kanan (Meter)</td>
                    <td style="text-align: center; border: 1px solid #cbd5e1;">4</td>
                    <td style="border: 1px solid #cbd5e1; padding: 4px;"><div style="background: #f1f5f9; border: 1px solid #f59e0b; height: 14px;"><div style="width: 100%; height: 100%; background: #fbbf24; font-size: 9px; font-weight: bold; text-align: center; line-height: 14px;">100%</div></div></td>
                    <td style="text-align: center; font-weight: bold; border: 1px solid #cbd5e1;">{{ $valHopR }}</td>
                    <td style="border: 1px solid #cbd5e1; padding: 4px;"><div style="background: #f1f5f9; border: 1px solid #0284c7; height: 14px;"><div style="width: {{ $pctHopR }}%; height: 100%; background: #38bdf8; font-size: 9px; font-weight: bold; text-align: center; line-height: 14px; color: #fff;">{{ $pctHopR }}%</div></div></td>
                </tr>
                <tr>
                    <td style="border: 1px solid #cbd5e1; padding: 5px;">Triple Hop Kiri (Meter)</td>
                    <td style="text-align: center; border: 1px solid #cbd5e1;">4</td>
                    <td style="border: 1px solid #cbd5e1; padding: 4px;"><div style="background: #f1f5f9; border: 1px solid #f59e0b; height: 14px;"><div style="width: 100%; height: 100%; background: #fbbf24; font-size: 9px; font-weight: bold; text-align: center; line-height: 14px;">100%</div></div></td>
                    <td style="text-align: center; font-weight: bold; border: 1px solid #cbd5e1;">{{ $valHopL }}</td>
                    <td style="border: 1px solid #cbd5e1; padding: 4px;"><div style="background: #f1f5f9; border: 1px solid #0284c7; height: 14px;"><div style="width: {{ $pctHopL }}%; height: 100%; background: #38bdf8; font-size: 9px; font-weight: bold; text-align: center; line-height: 14px; color: #fff;">{{ $pctHopL }}%</div></div></td>
                </tr>
                <!-- Agility -->
                <tr>
                    <td style="background-color: #0369a1; color: #fff; font-weight: bold; text-align: center; border: 1px solid #cbd5e1; padding: 5px;">Agility</td>
                    <td style="border: 1px solid #cbd5e1; padding: 5px;">Illinois Agility Test (Detik)</td>
                    <td style="text-align: center; border: 1px solid #cbd5e1;">19</td>
                    <td style="border: 1px solid #cbd5e1; padding: 4px;"><div style="background: #f1f5f9; border: 1px solid #f59e0b; height: 14px;"><div style="width: 100%; height: 100%; background: #fbbf24; font-size: 9px; font-weight: bold; text-align: center; line-height: 14px;">100%</div></div></td>
                    <td style="text-align: center; font-weight: bold; border: 1px solid #cbd5e1;">{{ $valIllinois }}</td>
                    <td style="border: 1px solid #cbd5e1; padding: 4px;"><div style="background: #f1f5f9; border: 1px solid #0284c7; height: 14px;"><div style="width: {{ $pctIllinois }}%; height: 100%; background: #38bdf8; font-size: 9px; font-weight: bold; text-align: center; line-height: 14px; color: #fff;">{{ $pctIllinois }}%</div></div></td>
                </tr>
                <!-- Endurance -->
                <tr>
                    <td rowspan="3" style="background-color: #0284c7; color: #fff; font-weight: bold; text-align: center; border: 1px solid #cbd5e1; padding: 5px;">Endurance</td>
                    <td style="border: 1px solid #cbd5e1; padding: 5px;">RAST Test - Average Power (Watt)</td>
                    <td style="text-align: center; border: 1px solid #cbd5e1;">275</td>
                    <td style="border: 1px solid #cbd5e1; padding: 4px;"><div style="background: #f1f5f9; border: 1px solid #f59e0b; height: 14px;"><div style="width: 100%; height: 100%; background: #fbbf24; font-size: 9px; font-weight: bold; text-align: center; line-height: 14px;">100%</div></div></td>
                    <td style="text-align: center; font-weight: bold; border: 1px solid #cbd5e1;">{{ $valRastP }}</td>
                    <td style="border: 1px solid #cbd5e1; padding: 4px;"><div style="background: #f1f5f9; border: 1px solid #0284c7; height: 14px;"><div style="width: {{ $pctRastP }}%; height: 100%; background: #38bdf8; font-size: 9px; font-weight: bold; text-align: center; line-height: 14px; color: #fff;">{{ $pctRastP }}%</div></div></td>
                </tr>
                <tr>
                    <td style="border: 1px solid #cbd5e1; padding: 5px;">RAST Test - Fatigue Index (Watt)</td>
                    <td style="text-align: center; border: 1px solid #cbd5e1;">5</td>
                    <td style="border: 1px solid #cbd5e1; padding: 4px;"><div style="background: #f1f5f9; border: 1px solid #f59e0b; height: 14px;"><div style="width: 100%; height: 100%; background: #fbbf24; font-size: 9px; font-weight: bold; text-align: center; line-height: 14px;">100%</div></div></td>
                    <td style="text-align: center; font-weight: bold; border: 1px solid #cbd5e1;">{{ $valRastF }}</td>
                    <td style="border: 1px solid #cbd5e1; padding: 4px;"><div style="background: #f1f5f9; border: 1px solid #0284c7; height: 14px;"><div style="width: {{ $pctRastF }}%; height: 100%; background: #38bdf8; font-size: 9px; font-weight: bold; text-align: center; line-height: 14px; color: #fff;">{{ $pctRastF }}%</div></div></td>
                </tr>
                <tr>
                    <td style="border: 1px solid #cbd5e1; padding: 5px;">Beep Test VO2Max (Ml/Kg/Min)</td>
                    <td style="text-align: center; border: 1px solid #cbd5e1;">45</td>
                    <td style="border: 1px solid #cbd5e1; padding: 4px;"><div style="background: #f1f5f9; border: 1px solid #f59e0b; height: 14px;"><div style="width: 100%; height: 100%; background: #fbbf24; font-size: 9px; font-weight: bold; text-align: center; line-height: 14px;">100%</div></div></td>
                    <td style="text-align: center; font-weight: bold; border: 1px solid #cbd5e1;">{{ $valBeep }}</td>
                    <td style="border: 1px solid #cbd5e1; padding: 4px;"><div style="background: #f1f5f9; border: 1px solid #0284c7; height: 14px;"><div style="width: {{ $pctBeep }}%; height: 100%; background: #38bdf8; font-size: 9px; font-weight: bold; text-align: center; line-height: 14px; color: #fff;">{{ $pctBeep }}%</div></div></td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- ==================== E. DATA VISUAL ==================== -->
    <div style="margin-bottom: 20px;">
        <div style="background-color: #e91e63; color: #ffffff; font-weight: 700; padding: 4px 12px; font-size: 13px;">
            E. DATA VISUAL
        </div>

        <div style="background-color: #383838; color: #ffffff; padding: 24px; border-radius: 0 0 6px 6px;">
            <!-- Baris Atas: Donut Besar + Radar Sejajar -->
            <div style="display: flex; gap: 20px; align-items: center; flex-wrap: wrap;">
                
                <!-- Donut Utama -->
                <div style="flex: 1; min-width: 250px; display: flex; flex-col; flex-direction: column; align-items: center; border-right: 1px solid #525252; padding-right: 15px;">
                    <div style="font-size: 13px; font-weight: bold; letter-spacing: 0.5px; text-transform: uppercase; margin-bottom: 15px; color: #e5e7eb;">
                        PROFIL FISIK ATLET
                    </div>
                    <div style="position: relative; width: 170px; height: 170px;">
                        <svg width="170" height="170" viewBox="0 0 100 100" style="transform: rotate(-90deg);">
                            <defs>
                                <linearGradient id="gradMainDirect" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" stop-color="#fbbf24" />
                                    <stop offset="100%" stop-color="#ec4899" />
                                </linearGradient>
                            </defs>
                            <circle cx="50" cy="50" r="38" stroke="#4b5563" stroke-width="12" fill="none" />
                            <circle cx="50" cy="50" r="38" stroke="url(#gradMainDirect)" stroke-width="12" stroke-linecap="round" fill="none"
                                stroke-dasharray="238.76"
                                stroke-dashoffset="{{ 238.76 - (238.76 * ($overall / 100)) }}" />
                        </svg>
                        <div style="position: absolute; inset: 0; display: flex; align-items: center; justify-content: center;">
                            <span style="font-size: 36px; font-weight: 900; color: #fbbf24;">{{ $overall }}%</span>
                        </div>
                    </div>
                </div>

                <!-- Radar Spider Chart -->
                <div style="flex: 1.5; min-width: 320px;">
                    <div style="font-size: 13px; font-weight: bold; letter-spacing: 0.5px; text-transform: uppercase; text-align: center; margin-bottom: 10px; color: #e5e7eb;">
                        DIAGRAM PROSENTASE HASIL TES FISIK
                    </div>
                    <div 
                        x-data="{
                            init() {
                                const waitChart = setInterval(() => {
                                    if (window.Chart) {
                                        clearInterval(waitChart);
                                        new window.Chart(this.$refs.radarDirect, {
                                            type: 'radar',
                                            data: {
                                                labels: {{ Js::from($radarLabels) }},
                                                datasets: [
                                                    {
                                                        label: 'Benchmark',
                                                        data: Array(10).fill(100),
                                                        borderColor: '#38bdf8',
                                                        borderWidth: 1.5,
                                                        backgroundColor: 'transparent',
                                                        pointRadius: 2,
                                                    },
                                                    {
                                                        label: 'Hasil',
                                                        data: {{ Js::from($radarValues) }},
                                                        borderColor: '#facc15',
                                                        borderWidth: 2,
                                                        backgroundColor: 'rgba(250, 204, 21, 0.25)',
                                                        pointRadius: 2.5,
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
                                                        ticks: { display: false, stepSize: 25 },
                                                        grid: { color: 'rgba(255, 255, 255, 0.2)' },
                                                        angleLines: { color: 'rgba(255, 255, 255, 0.2)' },
                                                        pointLabels: { color: '#d1d5db', font: { size: 9 } }
                                                    }
                                                },
                                                plugins: {
                                                    legend: {
                                                        position: 'bottom',
                                                        labels: { boxWidth: 10, color: '#9ca3af', font: { size: 10 } }
                                                    }
                                                }
                                            }
                                        });
                                    }
                                }, 50);
                            }
                        }"
                        style="height: 230px; position: relative; width: 100%;"
                    >
                        <canvas x-ref="radarDirect"></canvas>
                    </div>
                </div>
            </div>

            <!-- Garis Pemisah -->
            <div style="border-top: 1px solid #525252; margin: 20px 0;"></div>

            <!-- 5 Donut Mini Sejajar Horizontal -->
            <div style="display: flex; justify-content: space-between; gap: 10px; text-align: center;">
                @foreach ($pillars as $p)
                    <div style="flex: 1; display: flex; flex-direction: column; align-items: center;">
                        <span style="font-size: 11px; font-weight: 600; color: #d1d5db; margin-bottom: 8px;">
                            {{ $p['name'] }}
                        </span>

                        <div style="position: relative; width: 75px; height: 75px;">
                            <svg width="75" height="75" viewBox="0 0 100 100" style="transform: rotate(-90deg);">
                                <circle cx="50" cy="50" r="36" stroke="#4b5563" stroke-width="12" fill="none" />
                                <circle cx="50" cy="50" r="36" stroke="{{ $p['color'] }}" stroke-width="12" stroke-linecap="round" fill="none"
                                    stroke-dasharray="226.19"
                                    stroke-dashoffset="{{ 226.19 - (226.19 * ($p['val'] / 100)) }}" />
                            </svg>
                            <span style="position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: bold; color: #ffffff;">
                                {{ $p['val'] }}%
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- ==================== TIM PELATIH ==================== -->
    <div>
        <div style="background-color: #00adef; color: #ffffff; font-weight: 700; padding: 4px 12px; font-size: 13px;">
            TIM PELATIH
        </div>
        <table style="font-size: 12px; margin: 10px 0; border-collapse: collapse;">
            <tr style="height: 20px;"><td style="width: 80px; font-weight: bold;">Pelatih</td><td style="width: 15px;">:</td><td style="font-weight: bold;">HENNY WIJAYANTI</td></tr>
            <tr style="height: 20px;"><td style="font-weight: bold;">Pelatih</td><td>:</td><td style="font-weight: bold;">WULAN RAMADANI</td></tr>
            <tr style="height: 20px;"><td style="font-weight: bold;">Pelatih</td><td>:</td><td style="font-weight: bold;">M. TAQIYYUDIN ROIHANAN</td></tr>
        </table>
        <div style="height: 6px; background-color: #00adef; width: 100%; margin-top: 10px;"></div>
    </div>

</div>

@pushOnce('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpushOnce