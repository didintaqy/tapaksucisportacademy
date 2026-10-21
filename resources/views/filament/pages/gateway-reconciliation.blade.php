<x-filament-panels::page>
    <div class="space-y-6">
        <x-filament::section
            heading="Filter Rekonsiliasi"
            description="Bandingkan status pembayaran lokal dengan status transaksi terakhir dari payment gateway."
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
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Gateway</span>
                    <x-filament::input.wrapper>
                        <x-filament::input.select wire:model.live="gateway">
                            <option value="">Semua gateway</option>
                            @foreach ($gateways as $name)
                                <option value="{{ $name }}">{{ $name }}</option>
                            @endforeach
                        </x-filament::input.select>
                    </x-filament::input.wrapper>
                </label>

                <label class="flex items-center gap-2 pt-7">
                    <x-filament::input.checkbox wire:model.live="onlyMismatch" />
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Hanya yang tidak sinkron</span>
                </label>
            </div>

            <div class="mt-4 flex flex-wrap gap-3">
                <x-filament::button
                    wire:click="reconcileAll"
                    icon="heroicon-o-arrow-path"
                    color="primary"
                    wire:confirm="Selaraskan semua pembayaran yang tidak sinkron pada filter ini?"
                >
                    Selaraskan Semua
                </x-filament::button>

                <x-filament::button wire:click="resetFilters" color="gray" outlined>
                    Reset Filter
                </x-filament::button>
            </div>
        </x-filament::section>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-gray-900">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Transaksi Gateway</p>
                <p class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($summary['total'], 0, ',', '.') }}</p>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-gray-900">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Lunas</p>
                <p class="mt-1 text-2xl font-bold text-success-600 dark:text-success-400">{{ number_format($summary['paid'], 0, ',', '.') }}</p>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-gray-900">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Menunggu</p>
                <p class="mt-1 text-2xl font-bold text-warning-600 dark:text-warning-400">{{ number_format($summary['pending'], 0, ',', '.') }}</p>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-gray-900">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Tidak Sinkron</p>
                <p class="mt-1 text-2xl font-bold text-danger-600 dark:text-danger-400">{{ number_format($summary['mismatch'], 0, ',', '.') }}</p>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $summary['without_callback'] }} tanpa callback</p>
            </div>
        </div>

        <x-filament::section heading="Daftar Transaksi Gateway">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="border-b border-gray-200 text-xs uppercase tracking-wide text-gray-500 dark:border-white/10 dark:text-gray-400">
                        <tr>
                            <th class="py-2 pr-4">Invoice</th>
                            <th class="py-2 pr-4">Siswa</th>
                            <th class="py-2 pr-4">Gateway</th>
                            <th class="py-2 pr-4">Status Lokal</th>
                            <th class="py-2 pr-4">Status Gateway</th>
                            <th class="py-2 pr-4">Seharusnya</th>
                            <th class="py-2 pr-4">Callback</th>
                            <th class="py-2 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                        @forelse ($rows as $payment)
                            @php($expected = $payment->expectedStatusFromGateway())
                            <tr>
                                <td class="py-2 pr-4 font-mono text-xs text-gray-600 dark:text-gray-300">{{ $payment->invoice_number }}</td>
                                <td class="py-2 pr-4 font-medium text-gray-900 dark:text-white">
                                    {{ $payment->student?->full_name ?? '-' }}
                                </td>
                                <td class="py-2 pr-4 text-gray-600 dark:text-gray-300">{{ $payment->gateway_name ?? '-' }}</td>
                                <td class="py-2 pr-4">
                                    <x-filament::badge :color="$payment->status->getColor()" :icon="$payment->status->getIcon()">
                                        {{ $payment->status->getLabel() }}
                                    </x-filament::badge>
                                </td>
                                <td class="py-2 pr-4 font-mono text-xs text-gray-600 dark:text-gray-300">
                                    {{ $payment->gatewayTransactionStatus() ?? '—' }}
                                </td>
                                <td class="py-2 pr-4">
                                    @if ($expected)
                                        <x-filament::badge :color="$expected->getColor()">
                                            {{ $expected->getLabel() }}
                                        </x-filament::badge>
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>
                                <td class="py-2 pr-4 text-xs text-gray-600 dark:text-gray-300">
                                    {{ $payment->gatewayLastCallbackAt()?->format('d/m/Y H:i') ?? '-' }}
                                </td>
                                <td class="py-2 text-right">
                                    @if ($payment->hasGatewayMismatch())
                                        <x-filament::button
                                            size="xs"
                                            color="warning"
                                            wire:click="reconcile({{ $payment->id }})"
                                        >
                                            Selaraskan
                                        </x-filament::button>
                                    @else
                                        <x-filament::badge color="success" icon="heroicon-o-check">
                                            Sinkron
                                        </x-filament::badge>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-6 text-center text-gray-500 dark:text-gray-400">
                                    Tidak ada transaksi gateway pada filter ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
