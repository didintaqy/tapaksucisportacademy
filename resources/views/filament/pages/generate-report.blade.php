<x-filament-panels::page>
    <div class="max-w-3xl mx-auto">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">{{ $getTitle() }}</h1>
            <p class="text-gray-600 mt-1">Generate rapor performa siswa dalam format PDF</p>
        </div>

        <x-filament::section>
            <x-filament::form :schema="$getFormSchema()" :model="$getFormModel()" :state-path="$getFormStatePath()" />

            <div class="mt-6 flex gap-3">
                <x-filament::button wire:click="generateReport" class="w-full sm:w-auto">
                    <x-filament::icon name="heroicon-o-document-arrow-down" class="mr-2 h-4 w-4" />
                    Generate Rapor
                </x-filament::button>
            </div>
        </x-filament::section>

        <x-filament::section>
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h3 class="font-semibold text-blue-900 mb-2">Panduan Penggunaan</h3>
                <ul class="text-sm text-blue-800 space-y-1 list-disc list-inside">
                    <li>Pilih siswa yang ingin digenerate rapornya</li>
                    <li>Tentukan rentang tanggal periode rapor</li>
                    <li>Pilih format output (PDF untuk download, HTML untuk preview)</li>
                    <li>Klik "Generate Rapor" untuk membuat file</li>
                </ul>
                <p class="text-xs text-blue-700 mt-3">
                    Rapor mencakup: statistik kehadiran, detail kehadiran per sesi, prestasi/performa, dan riwayat UKT.
                </p>
            </div>
        </x-filament::section>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('showHtmlPreview', (event) => {
                const newWindow = window.open('', '_blank');
                newWindow.document.write(event.html);
                newWindow.document.close();
            });
        });
    </script>
    @endpush
</x-filament-panels::page>