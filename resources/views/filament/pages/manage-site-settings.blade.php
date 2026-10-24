<x-filament-panels::page>
    <form wire:submit="save" class="space-y-6">
        {{ $this->form }}

        <div class="flex items-center gap-3">
            <x-filament::button type="submit" icon="heroicon-o-check">
                Simpan Pengaturan
            </x-filament::button>

            <span class="text-sm text-gray-500 dark:text-gray-400">
                Perubahan langsung dipakai halaman publik setelah disimpan.
            </span>
        </div>
    </form>

    <x-filament::section class="mt-6">
        <div class="rounded-lg border border-info-200 bg-info-50 p-4 dark:border-info-700 dark:bg-info-950">
            <h3 class="font-semibold text-info-900 dark:text-info-100">Catatan Pengisian</h3>
            <ul class="mt-2 list-inside list-disc space-y-1 text-sm text-info-800 dark:text-info-200">
                <li>Kosongkan field yang datanya belum tersedia. Bagian terkait akan disembunyikan di situs publik.</li>
                <li>Peta hanya tampil jika kode embed Google Maps valid. Link lokasi tetap dapat dipakai sebagai tombol.</li>
                <li>Gunakan nomor WhatsApp dengan format 08xx atau 62xx; sistem menormalkannya secara otomatis.</li>
                <li>Iuran bulanan diatur per program melalui menu <strong>Program</strong>, bukan di halaman ini.</li>
            </ul>
        </div>
    </x-filament::section>
</x-filament-panels::page>
