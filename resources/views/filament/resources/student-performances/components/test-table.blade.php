@php
    $r = $getRecord();
    $analysis = app(\App\Services\Fitness\PerformanceAnalysisService::class)->analyze($r);
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
            @foreach ($analysis['items'] as $row)
                <tr class="hover:bg-gray-50/50 dark:hover:bg-white/[0.02]">
                    <td class="px-4 py-2.5 font-medium text-gray-900 dark:text-white">{{ $row['component_name'] }}</td>
                    <td class="px-4 py-2.5">{{ $row['name'] }}</td>
                    <td class="px-4 py-2.5 text-center font-mono text-gray-500">{{ $row['target'] }} {{ $row['unit'] }}</td>
                    <td class="px-4 py-2.5 text-center font-bold text-gray-900 dark:text-white font-mono">{{ $row['has_value'] ? $row['result'].' '.$row['unit'] : '-' }}</td>
                    <td class="px-4 py-2.5 text-center">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-amber-500/10 text-amber-600 dark:text-amber-400">
                            {{ $row['pct'] }}%
                        </span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
