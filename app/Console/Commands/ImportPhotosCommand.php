<?php

namespace App\Console\Commands;

use App\Models\Gallery;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ImportPhotosCommand extends Command
{
    protected $signature = 'photos:import
        {path : Folder sumber (relatif ke storage/app atau absolut)}
        {--disk=public : Disk Laravel tujuan}
        {--directory=galleries : Direktori tujuan di disk}
        {--category=Galeri : Kategori gallery}
        {--title=Kegiatan TSSA : Judul default}
        {--dry-run : Hanya tampilkan, tidak menulis}';

    protected $description = 'Impor foto dari folder ke tabel galleries (kompres + resize otomatis).';

    public function handle(): int
    {
        $source = $this->argument('path');
        if (! is_dir($source)) {
            // Coba relatif ke storage/app
            $candidate = storage_path('app/'.$source);
            if (is_dir($candidate)) {
                $source = $candidate;
            } else {
                $this->error("Folder tidak ditemukan: {$source}");

                return self::FAILURE;
            }
        }

        $disk = Storage::disk($this->option('disk'));
        $directory = trim($this->option('directory'), '/');
        $category = $this->option('category');
        $title = $this->option('title');
        $dryRun = (bool) $this->option('dry-run');

        $exts = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        $files = [];
        $rii = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($source));
        foreach ($rii as $file) {
            if ($file->isDir()) {
                continue;
            }
            if (in_array(strtolower($file->getExtension()), $exts, true)) {
                $files[] = $file->getPathname();
            }
        }

        sort($files);

        if (empty($files)) {
            $this->warn('Tidak ada file gambar ditemukan.');

            return self::SUCCESS;
        }

        $this->info('Ditemukan '.count($files).' gambar.');

        $imported = 0;
        foreach ($files as $index => $file) {
            $filename = 'gallery-'.now()->format('Ymd').'-'.str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT).'.jpg';
            $target = $directory.'/'.$filename;

            if ($dryRun) {
                $this->line("[DRY] {$file} -> {$target}");

                continue;
            }

            // Optimasi: resize + kompres via GD
            $info = getimagesize($file);
            if (! $info) {
                $this->warn("SKIP (bukan gambar): {$file}");

                continue;
            }

            $srcImg = match ($info['mime']) {
                'image/jpeg' => imagecreatefromjpeg($file),
                'image/png' => imagecreatefrompng($file),
                'image/webp' => imagecreatefromwebp($file),
                'image/gif' => imagecreatefromgif($file),
                default => null,
            };

            if (! $srcImg) {
                $this->warn("SKIP (format tidak didukung): {$file}");

                continue;
            }

            $w = imagesx($srcImg);
            $h = imagesy($srcImg);
            $maxW = 1600;
            $maxH = 1200;
            $scale = min($maxW / $w, $maxH / $h, 1.0);
            $nw = (int) round($w * $scale);
            $nh = (int) round($h * $scale);

            $dst = imagecreatetruecolor($nw, $nh);
            if ($info['mime'] === 'image/png') {
                imagealphablending($dst, false);
                imagesavealpha($dst, true);
                $transparent = imagecolorallocatealpha($dst, 0, 0, 0, 127);
                imagefill($dst, 0, 0, $transparent);
            }
            imagecopyresampled($dst, $srcImg, 0, 0, 0, 0, $nw, $nh, $w, $h);

            ob_start();
            imagejpeg($dst, null, 85);
            $binary = ob_get_clean();
            imagedestroy($srcImg);
            imagedestroy($dst);

            $disk->put($target, $binary);

            Gallery::create([
                'title' => $title.' #'.($index + 1),
                'category' => $category,
                'description' => null,
                'image' => $target,
                'is_published' => true,
                'sort_order' => $index + 1,
            ]);

            $imported++;
            $this->line('OK: '.basename($file)." -> {$target}");
        }

        $this->info("Selesai. {$imported} foto diimpor ke tabel galleries.");

        return self::SUCCESS;
    }
}
