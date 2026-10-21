<x-filament-panels::page>
    <div class="max-w-3xl">
        <x-filament::section>
            <form wire:submit="generateReport" class="space-y-6">
                {{ $this->form }}

                <div>
                    <x-filament::button type="submit" icon="heroicon-o-document-arrow-down">
                        Generate Rapor
                    </x-filament::button>
                </div>
            </form>
        </x-filament::section>

        <x-filament::section class="mt-6">
            <div class="rounded-lg border border-info-200 bg-info-50 p-4 dark:border-info-700 dark:bg-info-950">
                <h3 class="font-semibold text-info-900 dark:text-info-100">Panduan Penggunaan</h3>
                <ul class="mt-2 list-inside list-disc space-y-1 text-sm text-info-800 dark:text-info-200">
                    <li>Pilih siswa yang ingin digenerate rapornya</li>
                    <li>Tentukan rentang tanggal periode rapor</li>
                    <li>Pilih format output (PDF untuk download, HTML untuk preview)</li>
                    <li>Klik "Generate Rapor" untuk membuat file</li>
                </ul>
                <p class="mt-3 text-xs text-info-700 dark:text-info-300">
                    Rapor mencakup: statistik kehadiran, detail kehadiran per sesi, prestasi/performa, dan riwayat UKT.
                </p>
            </div>
        </x-filament::section>
    </div>

    @script
    <script>
        $wire.on('showHtmlPreview', (event) => {
            const newWindow = window.open('', '_blank');
            newWindow.document.write(event.html);
            newWindow.document.close();
        });
    </script>
    @endscript
</x-filament-panels::page>
