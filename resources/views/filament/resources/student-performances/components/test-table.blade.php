@php
    $r = $getRecord();
    $rows = [
        ['Flexibility', 'Sit & Reach (Cm)', '28 cm', $r->sit_and_reach ? "{$r->sit_and_reach} cm" : '-', $r->sit_and_reach ? round(($r->sit_and_reach/28)*100).'%' : '0%'],
        ['Strength', 'Push Up 1 Menit', '24 rep', $r->push_up_1_min ? "{$r->push_up_1_min} rep" : '-', $r->push_up_1_min ? round(($r->push_up_1_min/24)*100).'%' : '0%'],
        ['Strength', 'Sit Up 2 Menit', '45 rep', $r->sit_up_2_min ? "{$r->sit_up_2_min} rep" : '-', $r->sit_up_2_min ? round(($r->sit_up_2_min/45)*100).'%' : '0%'],
        ['Strength', 'Core Stability 12 Level', '12 lvl', $r->core_stability_level ? "{$r->core_stability_level} lvl" : '-', $r->core_stability_level ? round(($r->core_stability_level/12)*100).'%' : '0%'],
        ['Power', 'Triple Hop Kanan', '4.0 m', $r->triple_hop_right ? "{$r->triple_hop_right} m" : '-', $r->triple_hop_right ? round(($r->triple_hop_right/4.0)*100).'%' : '0%'],
        ['Power', 'Triple Hop Kiri', '4.0 m', $r->triple_hop_left ? "{$r->triple_hop_left} m" : '-', $r->triple_hop_left ? round(($r->triple_hop_left/4.0)*100).'%' : '0%'],
        ['Agility', 'Illinois Agility Test', '19.0 dtk', $r->illinois_agility ? "{$r->illinois_agility} dtk" : '-', $r->illinois_agility ? round((19.0/$r->illinois_agility)*100).'%' : '0%'],
        ['Endurance', 'RAST Average Power', '275 W', $r->rast_average_power ? "{$r->rast_average_power} W" : '-', $r->rast_average_power ? round(($r->rast_average_power/275)*100).'%' : '0%'],
        ['Endurance', 'RAST Fatigue Index', '5.0 W', $r->rast_fatigue_index ? "{$r->rast_fatigue_index} W" : '-', $r->rast_fatigue_index ? round((5.0/$r->rast_fatigue_index)*100).'%' : '0%'],
        ['Endurance', 'Beep Test VO2Max', '45.0', $r->beep_test_vo2max ?? '-', $r->beep_test_vo2max ? round(($r->beep_test_vo2max/45.0)*100).'%' : '0%'],
    ];
@endphp

<div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-white/10">
    <table class="w-full text-left text-sm">
        <thead class="bg-gray-50 text-xs uppercase font-semibold text-gray-500 dark:bg-white/5">
            <tr>
                <th class="px-4 py-3">Komponen</th>
                <th class="px-4 py-3">Item Tes</th>
                <th class="px-4 py-3 text-center">Benchmark</th>
                <th class="px-4 py-3 text-center">Hasil Tes</th>
                <th class="px-4 py-3 text-center">% Capaian</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-white/5">
            @foreach ($rows as $row)
                <tr class="hover:bg-gray-50/50 dark:hover:bg-white/[0.02]">
                    <td class="px-4 py-2.5 font-medium text-gray-900 dark:text-white">{{ $row[0] }}</td>
                    <td class="px-4 py-2.5">{{ $row[1] }}</td>
                    <td class="px-4 py-2.5 text-center font-mono text-gray-500">{{ $row[2] }}</td>
                    <td class="px-4 py-2.5 text-center font-bold text-gray-900 dark:text-white font-mono">{{ $row[3] }}</td>
                    <td class="px-4 py-2.5 text-center">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-amber-500/10 text-amber-600 dark:text-amber-400">
                            {{ $row[4] }}
                        </span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>