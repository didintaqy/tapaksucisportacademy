<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;
use Throwable;

class RestoreDatabase extends Command
{
    protected $signature = 'db:restore
                            {file : Berkas backup (mendukung .gz) atau direktori untuk verifikasi}
                            {--verify : Hanya verifikasi checksum dan isi backup, tanpa menulis database}
                            {--force : Wajib diisi bila database berisi data}';

    protected $description = 'Memulihkan database dari hasil db:backup (atau memverifikasi integritasnya)';

    public function handle(): int
    {
        $file = (string) $this->argument('file');

        try {
            if (! File::exists($file)) {
                $this->error("Berkas backup tidak ditemukan: {$file}");

                return self::FAILURE;
            }

            $checksumFile = $file.'.sha256';

            if (! File::exists($checksumFile)) {
                $this->error("Checksum tidak ditemukan: {$checksumFile}. Backup tidak dapat diverifikasi.");

                return self::FAILURE;
            }

            $expected = explode(' ', trim(File::get($checksumFile)))[0];
            $contents = $this->readBackup($file);
            $actual = hash('sha256', $contents);

            if (! hash_equals($expected, $actual)) {
                $this->error('Checksum tidak cocok. Berkas backup rusak atau sudah berubah.');

                return self::FAILURE;
            }

            $this->info('Checksum valid.');

            $driver = (string) config('database.default');

            if ($driver === 'sqlite') {
                $head = substr($contents, 0, 16);

                if (! str_starts_with($head, 'SQLite format 3')) {
                    $this->error('Isi berkas bukan database SQLite yang valid.');

                    return self::FAILURE;
                }
            } else {
                if (! str_contains($contents, 'CREATE TABLE')) {
                    $this->error('Isi berkas tidak memuat dump SQL yang valid.');

                    return self::FAILURE;
                }
            }

            $this->info(sprintf('Isi backup valid (%s, %s).', $driver, $this->humanSize(strlen($contents))));

            if ($this->option('verify')) {
                $this->info('Mode verifikasi: database tidak diubah.');

                return self::SUCCESS;
            }

            return $this->restore($file, $contents, $driver);
        } catch (Throwable $exception) {
            $this->error('Restore gagal: '.$exception->getMessage());

            return self::FAILURE;
        }
    }

    private function restore(string $file, string $contents, string $driver): int
    {
        if (! $this->option('force') && ! $this->isDatabaseEmpty()) {
            $this->error('Database masih berisi data. Jalankan ulang dengan --force (data akan ditimpa).');

            return self::FAILURE;
        }

        if ($driver === 'sqlite') {
            $target = (string) config('database.connections.sqlite.database');
            File::copy($file, $target.'.pre-restore');

            File::put($target, $contents);
            $this->info("Database SQLite dipulihkan dari {$file}. Salinan sebelum restore: {$target}.pre-restore");

            return self::SUCCESS;
        }

        $config = (array) config('database.connections.'.config('database.default'));

        $process = new Process([
            'mysql',
            '--host='.(string) ($config['host'] ?? '127.0.0.1'),
            '--port='.(string) ($config['port'] ?? '3306'),
            '--user='.(string) ($config['username'] ?? 'root'),
            '--default-character-set=utf8mb4',
            (string) ($config['database'] ?? ''),
        ]);

        $process->setEnv(array_filter([
            'MYSQL_PWD' => (string) ($config['password'] ?? ''),
        ], fn ($value): bool => $value !== ''));

        $process->setInput($contents);
        $process->setTimeout(600);
        $process->run();

        if (! $process->isSuccessful()) {
            $this->error('Restore MySQL gagal: '.trim($process->getErrorOutput()));

            return self::FAILURE;
        }

        $this->info("Database MySQL dipulihkan dari {$file}.");

        return self::SUCCESS;
    }

    private function isDatabaseEmpty(): bool
    {
        try {
            return count(DB::select(
                'SELECT table_name FROM information_schema.tables WHERE table_schema = DATABASE()'
            )) === 0;
        } catch (Throwable) {
            // Driver tanpa information_schema dianggap berisi data (aman).
            return false;
        }
    }

    private function readBackup(string $file): string
    {
        if (! str_ends_with($file, '.gz')) {
            return File::get($file);
        }

        $handle = gzopen($file, 'rb');
        $contents = '';

        while (! gzeof($handle)) {
            $contents .= gzread($handle, 1048576);
        }

        gzclose($handle);

        return $contents;
    }

    private function humanSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $power = $bytes > 0 ? (int) floor(log($bytes, 1024)) : 0;

        return round($bytes / (1024 ** $power), 2).' '.$units[$power];
    }
}
