<?php

namespace App\Console\Commands;

use App\Services\SiteSettingMedia;
use Illuminate\Console\Command;

class PruneSiteSettingMedia extends Command
{
    protected $signature = 'site-settings:prune-media
                            {--dry-run : Tampilkan berkas yang akan dipindahkan tanpa mengubah apa pun}
                            {--permanent : Hapus permanen, tanpa karantina (tidak bisa dipulihkan)}
                            {--list : Tampilkan isi karantina beserta ukurannya}
                            {--restore=* : Pulihkan path tertentu dari karantina, mis. --restore=site/logo.png}
                            {--batch= : Batch karantina yang dipakai (default: terbaru)}
                            {--empty-trash : Hapus permanen seluruh isi karantina}
                            {--keep-days= : Umur maksimum karantina dalam hari (default dari config)}';

    protected $description = 'Mengelola gambar Pengaturan Situs yang sudah tidak dipakai (karantina & pulihkan)';

    public function handle(SiteSettingMedia $media): int
    {
        if ($this->option('list')) {
            return $this->listQuarantine($media);
        }

        if ($this->option('restore')) {
            return $this->restore($media, (array) $this->option('restore'));
        }

        if ($this->option('empty-trash')) {
            $count = $media->emptyTrash($this->option('batch'));

            $count > 0
                ? $this->info("Karantina dikosongkan: {$count} berkas dihapus permanen.")
                : $this->info('Karantina sudah kosong.');

            return self::SUCCESS;
        }

        return $this->prune($media);
    }

    private function prune(SiteSettingMedia $media): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $quarantine = ! $this->option('permanent');
        $removed = $media->prune(dryRun: $dryRun, quarantine: $quarantine);
        $expired = $this->expireTrash($media, $dryRun);

        if ($removed === []) {
            $this->info('Tidak ada gambar yatim. Semua gambar site settings masih dipakai.');

            return self::SUCCESS;
        }

        $prefix = match (true) {
            $dryRun => '[akan dipindahkan] ',
            $quarantine => '[dikarantina] ',
            default => '[dihapus] ',
        };

        foreach ($removed as $file) {
            $this->line($prefix.$file);
        }

        if ($dryRun) {
            $this->info('Ditemukan '.count($removed).' gambar yatim (dry run, tidak ada yang diubah).');
        } elseif ($quarantine) {
            $this->info('Berhasil mengarantina '.count($removed).' gambar yatim.');
            $this->line('Lihat dengan: php artisan site-settings:prune-media --list');
        } else {
            $this->info('Berhasil menghapus permanen '.count($removed).' gambar yatim.');
        }

        return self::SUCCESS;
    }

    /**
     * Terapkan retensi karantina dan laporkan hasilnya.
     *
     * @return array<int, string> Batch yang kedaluwarsa
     */
    private function expireTrash(SiteSettingMedia $media, bool $dryRun): array
    {
        $keepDays = $this->option('keep-days');
        $keepDays = $keepDays === null
            ? (int) config('site_settings.trash_retention_days', 30)
            : (int) $keepDays;

        if ($keepDays <= 0) {
            return [];
        }

        $expired = $media->pruneTrash($keepDays, $dryRun);

        if ($expired === []) {
            return [];
        }

        foreach ($expired as $batch) {
            $this->line(($dryRun ? '[akan kedaluwarsa] ' : '[kedaluwarsa] ').$batch);
        }

        $this->info(sprintf(
            '%s %d batch karantina lebih tua dari %d hari.',
            $dryRun ? 'Ditemukan' : 'Menghapus permanen',
            count($expired),
            $keepDays,
        ));

        return $expired;
    }

    private function listQuarantine(SiteSettingMedia $media): int
    {
        $batches = $media->quarantinedBatches();

        if ($batches === []) {
            $this->info('Karantina kosong.');

            return self::SUCCESS;
        }

        foreach ($batches as $batch => $files) {
            $this->line(sprintf(
                '<comment>%s</comment> (%d berkas, %s)',
                $batch,
                count($files),
                $this->humanSize($media->trashSize($batch)),
            ));

            foreach ($files as $file) {
                $this->line('  '.$file);
            }
        }

        $this->line('');
        $this->line(sprintf(
            'Total: %d batch, %s. Retensi: %d hari.',
            count($batches),
            $this->humanSize($media->trashSize()),
            (int) config('site_settings.trash_retention_days', 30),
        ));
        $this->line('Lokasi: '.storage_path('app/private/'.SiteSettingMedia::TRASH_DIRECTORY));

        return self::SUCCESS;
    }

    private function humanSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $power = $bytes > 0 ? (int) floor(log($bytes, 1024)) : 0;
        $power = min($power, count($units) - 1);

        return round($bytes / (1024 ** $power), 2).' '.$units[$power];
    }

    /**
     * @param  array<int, string>  $paths
     */
    private function restore(SiteSettingMedia $media, array $paths): int
    {
        $restored = $media->restore($paths, $this->option('batch'));

        if ($restored === []) {
            $this->warn('Tidak ada berkas yang dipulihkan. Cek nama path dan batch dengan --list.');

            return self::FAILURE;
        }

        foreach ($restored as $file) {
            $this->line('[dipulihkan] '.$file);
        }

        $this->info(count($restored).' berkas dipulihkan ke disk publik.');
        $this->line('Berkas kini tersedia di penyimpanan, tetapi belum otomatis dipakai oleh field manapun.');

        return self::SUCCESS;
    }
}
