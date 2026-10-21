<x-filament-panels::page>
    <div class="space-y-6">
        <x-filament::section
            heading="Filter Laporan"
            description="Pilih rentang tanggal, jenis pembayaran, dan metode bayar."
        >
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <label class="space-y-1">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Dari Tanggal</span>
                    <x-filament::input.wrapper>
                        <x-filament::input type="date" wire:model.live="from" />
                    </x-filament::input.wrapper>
                </label>

                <label class="space-y-1">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Sampai Tanggal</span>
                    <x-filament::input.wrapper>
                        <x-filament::input type="date" wire:model.live="to" />
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

                <label class="space-y-1">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Metode Bayar</span>
                    <x-filament::input.wrapper>
                        <x-filament::input.select wire:model.live="method">
                            <option value="">Semua metode</option>
                            @foreach ($methods as $paymentMethod)
                                <option value="{{ $paymentMethod->value }}">{{ $paymentMethod->getLabel() }}</option>
                            @endforeach
                        </x-filament::input.select>
                    </x-filament::input.wrapper>
                </label>
            </div>

            <div class="mt-4 flex flex-wrap gap-3">
                <x-filament::button
                    tag="a"
                    :href="route('admin.reports.income.excel', ['from' => $fromDate->toDateString(), 'to' => $toDate->toDateString(), 'type' => $type, 'method' => $method])"
                    icon="heroicon-o-table-cells"
                    color="success"
                >
                    Export Excel
                </x-filament::button>

                <x-filament::button
                    tag="a"
                    :href="route('admin.reports.income.export', ['from' => $fromDate->toDateString(), 'to' => $toDate->toDateString(), 'type' => $type, 'method' => $method])"
                    icon="heroicon-o-arrow-down-tray"
                    color="gray"
                >
                    Export CSV
                </x-filament::button>

                <x-filament::button
                    tag="a"
                    :href="route('admin.reports.income.pdf', ['from' => $fromDate->toDateString(), 'to' => $toDate->toDateString(), 'type' => $type, 'method' => $method])"
                    icon="heroicon-o-document-arrow-down"
                    color="gray"
                >
                    Export PDF
                </x-filament::button>

                <x-filament::button wire:click="resetFilters" color="gray" outlined>
                    Reset Filter
                </x-filament::button>
            </div>
        </x-filament::section>

        <div class="grid gap-4 sm:grid-cols-3">
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-gray-900">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Pemasukan</p>
                <p class="mt-1 text-2xl font-bold text-success-600 dark:text-success-400">
                    Rp {{ number_format($summary['total'], 0, ',', '.') }}
                </p>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-gray-900">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Jumlah Transaksi</p>
                <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">
                    {{ number_format($summary['count'], 0, ',', '.') }}
                </p>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-gray-900">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Rata-rata per Transaksi</p>
                <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">
                    Rp {{ number_format($summary['average'], 0, ',', '.') }}
                </p>
            </div>
        </div>

        <x-filament::section
            heading="Rekap per Bulan"
            description="{{ $fromDate->translatedFormat('d F Y') }} — {{ $toDate->translatedFormat('d F Y') }}"
        >
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-gray-200 text-xs uppercase tracking-wide text-gray-500 dark:border-white/10 dark:text-gray-400">
                        <tr>
                            <th class="py-2 pr-4">Periode</th>
                            <th class="py-2 pr-4 text-right">Transaksi</th>
                            <th class="py-2 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                        @forelse ($byMonth as $row)
                            <tr>
                                <td class="py-2 pr-4 font-medium text-gray-900 dark:text-white">{{ $row['label'] }}</td>
                                <td class="py-2 pr-4 text-right text-gray-600 dark:text-gray-300">{{ number_format($row['transactions'], 0, ',', '.') }}</td>
                                <td class="py-2 text-right font-semibold text-gray-900 dark:text-white">Rp {{ number_format($row['total'], 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-4 text-center text-gray-500 dark:text-gray-400">Tidak ada data.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="border-t border-gray-200 dark:border-white/10">
                        <tr>
                            <th class="py-2 pr-4 text-left text-gray-900 dark:text-white">Total</th>
                            <th class="py-2 pr-4 text-right text-gray-900 dark:text-white">{{ number_format($summary['count'], 0, ',', '.') }}</th>
                            <th class="py-2 text-right text-gray-900 dark:text-white">Rp {{ number_format($summary['total'], 0, ',', '.') }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </x-filament::section>

        <x-filament::section heading="Rekap per Jenis Pembayaran">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-gray-200 text-xs uppercase tracking-wide text-gray-500 dark:border-white/10 dark:text-gray-400">
                        <tr>
                            <th class="py-2 pr-4">Jenis</th>
                            <th class="py-2 pr-4 text-right">Transaksi</th>
                            <th class="py-2 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                        @forelse ($byType as $row)
                            <tr>
                                <td class="py-2 pr-4 font-medium text-gray-900 dark:text-white">
                                    {{ $row->paymentType?->name ?? 'Tanpa jenis' }}
                                </td>
                                <td class="py-2 pr-4 text-right text-gray-600 dark:text-gray-300">{{ number_format((int) $row->transactions, 0, ',', '.') }}</td>
                                <td class="py-2 text-right font-semibold text-gray-900 dark:text-white">Rp {{ number_format((float) $row->total, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-4 text-center text-gray-500 dark:text-gray-400">Tidak ada data.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
