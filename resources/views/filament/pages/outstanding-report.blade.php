<x-filament-panels::page>
    <div class="space-y-6">
        <x-filament::section
            heading="Filter Tunggakan"
            description="Cari siswa atau filter berdasarkan jenis pembayaran."
        >
            <div class="grid gap-4 sm:grid-cols-2">
                <label class="space-y-1">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Cari Siswa</span>
                    <x-filament::input.wrapper>
                        <x-filament::input
                            type="search"
                            wire:model.live.debounce.400ms="search"
                            placeholder="Nama, kode siswa, atau NIK"
                        />
                    </x-filament::input.wrapper>
                </label>

                <label class="space-y-1">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Jenis Pembayaran</span>
                    <x-filament::input.wrapper>
                        <x-filament::input.select wire:model.live="type">
                            <option value="">Semua jenis</option>
                            @foreach ($types as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </x-filament::input.select>
                    </x-filament::input.wrapper>
                </label>
            </div>

            <div class="mt-4 flex flex-wrap gap-3">
                <x-filament::button
                    tag="a"
                    :href="route('admin.reports.outstanding.excel', ['type' => $type, 'search' => $search])"
                    icon="heroicon-o-table-cells"
                    color="success"
                >
                    Export Excel
                </x-filament::button>

                <x-filament::button
                    tag="a"
                    :href="route('admin.reports.outstanding.export', ['type' => $type, 'search' => $search])"
                    icon="heroicon-o-arrow-down-tray"
                    color="gray"
                >
                    Export CSV
                </x-filament::button>

                <x-filament::button
                    tag="a"
                    :href="route('admin.reports.outstanding.pdf', ['type' => $type, 'search' => $search])"
                    icon="heroicon-o-document-arrow-down"
                    color="gray"
                >
                    Export PDF
                </x-filament::button>

                <form method="POST" action="{{ route('admin.reports.queue-export') }}" class="inline">
                    @csrf
                    <input type="hidden" name="report_type" value="outstanding">
                    <input type="hidden" name="type" value="{{ $type }}">
                    <input type="hidden" name="search" value="{{ $search }}">
                    <x-filament::button type="submit" icon="heroicon-o-clock" color="info" outlined>
                        Export via Antrean
                    </x-filament::button>
                </form>

                <x-filament::button wire:click="resetFilters" color="gray" outlined>
                    Reset Filter
                </x-filament::button>
            </div>
        </x-filament::section>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-gray-900">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Tunggakan</p>
                <p class="mt-1 text-2xl font-bold text-danger-600 dark:text-danger-400">
                    Rp {{ number_format($summary['total'], 0, ',', '.') }}
                </p>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-gray-900">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Jumlah Tagihan</p>
                <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($summary['count'], 0, ',', '.') }}</p>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-gray-900">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Siswa Menunggak</p>
                <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($summary['students'], 0, ',', '.') }}</p>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-gray-900">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Lewat Jatuh Tempo</p>
                <p class="mt-1 text-2xl font-bold text-warning-600 dark:text-warning-400">{{ number_format($summary['overdue'], 0, ',', '.') }}</p>
            </div>
        </div>

        <x-filament::section heading="Daftar Tunggakan per Siswa">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-gray-200 text-xs uppercase tracking-wide text-gray-500 dark:border-white/10 dark:text-gray-400">
                        <tr>
                            <th class="py-2 pr-4">Kode</th>
                            <th class="py-2 pr-4">Nama Siswa</th>
                            <th class="py-2 pr-4">Cabang</th>
                            <th class="py-2 pr-4 text-center">Tagihan</th>
                            <th class="py-2 pr-4">Jatuh Tempo Terlama</th>
                            <th class="py-2 text-right">Total Tunggakan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                        @forelse ($rows as $row)
                            <tr>
                                <td class="py-2 pr-4 font-mono text-xs text-gray-600 dark:text-gray-300">
                                    {{ $row->student?->student_code ?? '-' }}
                                </td>
                                <td class="py-2 pr-4 font-medium text-gray-900 dark:text-white">
                                    {{ $row->student?->full_name ?? 'Siswa terhapus' }}
                                </td>
                                <td class="py-2 pr-4 text-gray-600 dark:text-gray-300">{{ $row->student?->branch ?? '-' }}</td>
                                <td class="py-2 pr-4 text-center text-gray-600 dark:text-gray-300">{{ (int) $row->bills }}</td>
                                <td class="py-2 pr-4 text-gray-600 dark:text-gray-300">
                                    {{ $row->oldest_due_date ? \Illuminate\Support\Carbon::parse($row->oldest_due_date)->format('d/m/Y') : '-' }}
                                </td>
                                <td class="py-2 text-right font-semibold text-danger-600 dark:text-danger-400">
                                    Rp {{ number_format((float) $row->total, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-6 text-center text-gray-500 dark:text-gray-400">
                                    Tidak ada tunggakan. 🎉
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="border-t border-gray-200 dark:border-white/10">
                        <tr>
                            <th colspan="5" class="py-2 pr-4 text-left text-gray-900 dark:text-white">Total</th>
                            <th class="py-2 text-right text-gray-900 dark:text-white">
                                Rp {{ number_format($summary['total'], 0, ',', '.') }}
                            </th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
