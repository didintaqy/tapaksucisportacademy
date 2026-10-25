<?php

namespace App\Services;

use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

/**
 * Mengumpulkan dan membersihkan berkas gambar milik Pengaturan Situs.
 *
 * Semua gambar site settings disimpan pada disk "public" dalam beberapa
 * direktori tetap. Saat admin mengganti gambar lewat Filament, berkas lama
 * tidak terhapus otomatis; kelas ini menyediakan cara aman untuk:
 *
 * - mengetahui berkas mana saja yang masih dipakai (referencedPaths), dan
 * - menghapus berkas yatim di direktori tersebut (prune).
 *
 * Hanya path yang aman dihapus: relatif, berada di direktori milik fitur,
 * dan bukan aset tema bawaan (prefix "images/"). URL eksternal dan path
 * absolut selalu dilewati.
 */
class SiteSettingMedia
{
    public const DISK = 'public';

    /**
     * Disk tujuan karantina berkas yatim.
     *
     * Disk "local" mengarah ke storage/app/private sehingga berkas yang
     * dikarantina tidak bisa diakses lewat URL publik. Ini memungkinkan
     * admin memulihkan gambar bila terhapus karena salah ganti.
     */
    public const TRASH_DISK = 'local';

    public const TRASH_DIRECTORY = 'site-media-trash';

    /**
     * Direktori disk "public" yang sepenuhnya dikelola oleh Pengaturan Situs.
     *
     * Jangan menambahkan direktori yang mungkin menyimpan berkas milik
     * fitur lain agar prune tidak menghapus data yang salah.
     *
     * @var array<int, string>
     */
    public const DIRECTORIES = [
        'site',
        'why-choose',
        'why-choose-items',
        'backgrounds',
    ];

    /**
     * Nama field pada data settings yang berisi path gambar.
     *
     * Menggunakan sintaks dot data_get. Repeater (why_items) ditangani
     * secara dinamis oleh referencedPaths().
     *
     * @var array<int, string>
     */
    public const FIELDS = [
        'identity.logo',
        'identity.favicon',
        'identity.page_header_image',
        'contact.image',
        'hero.image',
        'home.feature_image',
        'home.why_image_1',
        'home.why_image_2',
        'home.testimonials_background',
        'home.dark_section_background',
        'home.core_feature_background',
        'about.image',
        'about.video_image',
        'seo.image',
        'footer.background_image',
    ];

    public function __construct(
        private readonly SiteSettings $settings,
        private readonly string $disk = self::DISK,
    ) {}

    /**
     * Semua path relatif pada disk yang masih direferensikan settings.
     *
     * @param  array<string, mixed>|null  $data
     * @return array<int, string>
     */
    public function referencedPaths(?array $data = null): array
    {
        $data ??= $this->settings->all();

        $paths = [];

        foreach (self::FIELDS as $field) {
            $this->collect(data_get($data, $field), $paths);
        }

        // Gambar pada repeater "Keunggulan" di beranda.
        foreach ((array) data_get($data, 'home.why_items', []) as $item) {
            $this->collect(is_array($item) ? ($item['image'] ?? null) : null, $paths);
        }

        $paths = array_values(array_unique(array_filter($paths, fn (string $path): bool => $this->isPrunablePath($path))));

        sort($paths);

        return $paths;
    }

    /**
     * Hapus berkas yatim di DIRECTORY milik fitur ini.
     *
     * Bila $quarantine bernilai true, berkas dipindahkan ke disk karantina
     * (bukan dihapus permanen) sehingga masih bisa dipulihkan.
     *
     * @param  array<int, string>|null  $data
     * @return array<int, string> Path relatif yang dihapus/dikarantina
     */
    public function prune(?array $data = null, bool $dryRun = false, bool $quarantine = true): array
    {
        $referenced = array_flip($this->referencedPaths($data));
        $disk = Storage::disk($this->disk);
        $removed = [];

        foreach (self::DIRECTORIES as $directory) {
            if (! $disk->directoryExists($directory)) {
                continue;
            }

            foreach ($disk->allFiles($directory) as $file) {
                // Berkas .gitignore dsb. bukan gambar, jangan disentuh.
                if (in_array(basename($file), ['.gitignore', '.gitkeep'], true)) {
                    continue;
                }

                if (isset($referenced[$file])) {
                    continue;
                }

                if (! $dryRun) {
                    $quarantine ? $this->moveToTrash($file) : $disk->delete($file);
                }

                $removed[] = $file;
            }
        }

        sort($removed);

        return $removed;
    }

    /**
     * Pindahkan berkas ke disk karantina dengan cap waktu agar nama unik.
     *
     * Penamaan: <dir trash>/<YYYYmmdd-His>/<path asli>
     */
    private function moveToTrash(string $path): bool
    {
        $source = Storage::disk($this->disk);
        $trash = Storage::disk(self::TRASH_DISK);
        $target = self::TRASH_DIRECTORY.'/'.now()->format('Ymd-His').'/'.$path;

        // Berkas dengan nama sama di batch yang sama tidak mungkin terjadi
        // karena path asli unik, tetapi jaga-jaga bila file tertinggal.
        if ($trash->exists($target)) {
            $target = self::TRASH_DIRECTORY.'/'.now()->format('Ymd-His').'-'.Str::random(6).'/'.$path;
        }

        if (! $source->exists($path)) {
            return false;
        }

        // Salin isi lalu hapus berkas asli. Cara ini bekerja sama baiknya
        // untuk disk lokal maupun disk yang berbeda (mis. S3 -> lokal).
        $trash->put($target, $source->get($path));

        if (! $trash->exists($target)) {
            return false;
        }

        return $source->delete($path);
    }

    /**
     * Hapus permanen batch karantina yang lebih tua dari $days hari.
     *
     * Umur dibaca dari cap waktu pada nama folder batch (Ymd-His), bukan
     * dari mtime berkas, sehingga hasilnya konsisten walau berkas disalin
     * atau dipulihkan dari backup. Folder yang namanya tidak dikenali
     * dilewati, bukan dihapus.
     *
     * @param  int  $days  Umur maksimum dalam hari; 0 atau kurang berarti tanpa retensi
     * @return array<int, string> Batch yang dihapus
     */
    public function pruneTrash(int $days, bool $dryRun = false): array
    {
        if ($days <= 0) {
            return [];
        }

        $trash = Storage::disk(self::TRASH_DISK);
        $cutoff = now()->subDays($days);
        $deleted = [];

        foreach (array_keys($this->quarantinedBatches()) as $batch) {
            $createdAt = $this->batchTimestamp($batch);

            // Nama folder tak dikenali: jangan tebak-tebakan, biarkan admin.
            if ($createdAt === null || $createdAt->greaterThanOrEqualTo($cutoff)) {
                continue;
            }

            if (! $dryRun) {
                $trash->deleteDirectory(self::TRASH_DIRECTORY.'/'.$batch);
            }

            $deleted[] = $batch;
        }

        sort($deleted);

        return $deleted;
    }

    /**
     * Baca cap waktu dari nama folder batch.
     *
     * Nama batch dibuat oleh moveToTrash(): "Ymd-His" atau
     * "Ymd-His-<random>" bila terjadi bentrok nama.
     */
    private function batchTimestamp(string $batch): ?CarbonInterface
    {
        if (preg_match('/^(\d{8}-\d{6})/', $batch, $matches) !== 1) {
            return null;
        }

        try {
            return Carbon::createFromFormat('Ymd-His', $matches[1]);
        } catch (Throwable) {
            return null;
        }
    }

    /**
     * Total ukuran berkas dalam karantina (byte).
     */
    public function trashSize(?string $batch = null): int
    {
        $trash = Storage::disk(self::TRASH_DISK);
        $directory = $batch === null
            ? self::TRASH_DIRECTORY
            : self::TRASH_DIRECTORY.'/'.$batch;

        if (! $trash->directoryExists($directory)) {
            return 0;
        }

        $size = 0;

        foreach ($trash->allFiles($directory) as $file) {
            $size += $trash->size($file) ?: 0;
        }

        return $size;
    }

    /**
     * Daftar batch karantina beserta isinya.
     *
     * @return array<string, array<int, string>> Batch => daftar path asli
     */
    public function quarantinedBatches(): array
    {
        $trash = Storage::disk(self::TRASH_DISK);

        if (! $trash->directoryExists(self::TRASH_DIRECTORY)) {
            return [];
        }

        $batches = [];

        foreach ($trash->directories(self::TRASH_DIRECTORY) as $batchDirectory) {
            $batch = basename($batchDirectory);
            $files = [];

            foreach ($trash->allFiles($batchDirectory) as $file) {
                $files[] = Str::after($file, $batchDirectory.'/');
            }

            sort($files);
            $batches[$batch] = $files;
        }

        krsort($batches);

        return $batches;
    }

    /**
     * Pulihkan berkas dari karantina ke disk publik.
     *
     * @param  array<int, string>  $paths  Path asli, mis. "site/logo.png"
     * @param  string|null  $batch  Batch tertentu; null berarti yang terbaru
     * @return array<int, string> Path yang berhasil dipulihkan
     */
    public function restore(array $paths, ?string $batch = null, bool $overwrite = false): array
    {
        $batches = $this->quarantinedBatches();

        if ($batch === null) {
            $batch = array_key_first($batches);
        }

        if ($batch === null || ! isset($batches[$batch])) {
            return [];
        }

        $source = Storage::disk($this->disk);
        $trash = Storage::disk(self::TRASH_DISK);
        $restored = [];

        foreach ($paths as $path) {
            if (! $this->isPrunablePath($path) || ! in_array($path, $batches[$batch], true)) {
                continue;
            }

            $trashed = self::TRASH_DIRECTORY.'/'.$batch.'/'.$path;

            if (! $trash->exists($trashed)) {
                continue;
            }

            // Jangan timpa berkas yang sedang dipakai settings.
            if (! $overwrite && $source->exists($path)) {
                continue;
            }

            $source->makeDirectory(dirname($path));

            if ($this->restoreFromTrash($trashed, $path)) {
                $restored[] = $path;
            }
        }

        // Bersihkan folder batch bila sudah kosong.
        if ($trash->allFiles(SiteSettingMedia::TRASH_DIRECTORY.'/'.$batch) === []) {
            $trash->deleteDirectory(SiteSettingMedia::TRASH_DIRECTORY.'/'.$batch);
        }

        sort($restored);

        return $restored;
    }

    private function restoreFromTrash(string $trashed, string $path): bool
    {
        $trash = Storage::disk(self::TRASH_DISK);

        if (! $trash->exists($trashed)) {
            return false;
        }

        Storage::disk($this->disk)->put($path, $trash->get($trashed));

        return $trash->delete($trashed);
    }

    /**
     * Hapus permanen seluruh isi karantina.
     *
     * @return int Jumlah berkas yang dihapus
     */
    public function emptyTrash(?string $batch = null): int
    {
        $trash = Storage::disk(self::TRASH_DISK);
        $directory = $batch === null
            ? self::TRASH_DIRECTORY
            : self::TRASH_DIRECTORY.'/'.$batch;

        if (! $trash->directoryExists($directory)) {
            return 0;
        }

        $count = count($trash->allFiles($directory));
        $trash->deleteDirectory($directory);

        return $count;
    }

    /**
     * Apakah path boleh dihapus oleh prune.
     */
    public function isPrunablePath(?string $path): bool
    {
        if (blank($path)) {
            return false;
        }

        $path = trim($path);

        // URL eksternal, protocol-relative, atau path absolut.
        if (Str::startsWith($path, ['http://', 'https://', '//', '/', '\\'])) {
            return false;
        }

        // Windows drive atau traversal.
        if (preg_match('/^[a-zA-Z]:/', $path) === 1 || str_contains($path, '..')) {
            return false;
        }

        $directory = Str::beforeLast($path, '/');

        return in_array($directory, self::DIRECTORIES, true);
    }

    /**
     * @param  array<int, string>  $paths
     */
    private function collect(mixed $value, array &$paths): void
    {
        if (is_string($value) && $value !== '') {
            $paths[] = trim($value);
        }
    }
}
