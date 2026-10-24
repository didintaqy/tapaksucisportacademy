<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class BackupDatabase extends Command
{
    protected $signature = 'db:backup
                            {--path= : Direktori tujuan backup (default: storage/app/backups)}
                            {--keep=7 : Jumlah backup terakhir yang dipertahankan}';

    protected $description = 'Membuat backup database aplikasi (SQLite/MySQL)';

    public function handle(): int
    {
        $connection = (string) config('database.default');
        $directory = (string) ($this->option('path') ?: storage_path('app/backups'));

        File::ensureDirectoryExists($directory);

        $filename = sprintf(
            'database-%s.%s',
            now()->format('Ymd_His'),
            $connection === 'mysql' ? 'sql' : 'sqlite',
        );

        $target = $directory.DIRECTORY_SEPARATOR.$filename;

        if ($connection === 'sqlite') {
            $quotedTarget = DB::getPdo()->quote($target);
            DB::statement("VACUUM INTO {$quotedTarget}");
        } elseif ($connection === 'mysql') {
            $config = (array) config('database.connections.mysql');
            $password = (string) ($config['password'] ?? '');

            if ($password !== '') {
                putenv('MYSQL_PWD='.$password);
            }

            $command = sprintf(
                'mysqldump --host=%s --port=%s --user=%s --single-transaction --quick %s > %s',
                escapeshellarg((string) ($config['host'] ?? '127.0.0.1')),
                escapeshellarg((string) ($config['port'] ?? '3306')),
                escapeshellarg((string) ($config['username'] ?? 'root')),
                escapeshellarg((string) ($config['database'] ?? '')),
                escapeshellarg($target),
            );

            exec($command, $output, $exitCode);

            if ($exitCode !== 0) {
                $this->error('Backup MySQL gagal. Pastikan mysqldump tersedia dan kredensial benar.');

                return self::FAILURE;
            }
        } else {
            $this->error("Driver database [{$connection}] belum didukung untuk backup otomatis.");

            return self::FAILURE;
        }

        $this->pruneOldBackups($directory, (int) $this->option('keep'));

        $this->info("Backup database berhasil: {$target}");

        return self::SUCCESS;
    }

    private function pruneOldBackups(string $directory, int $keep): void
    {
        if ($keep < 1) {
            return;
        }

        $backups = collect(File::files($directory))
            ->filter(fn (\SplFileInfo $file): bool => str_starts_with($file->getFilename(), 'database-'))
            ->sortByDesc(fn (\SplFileInfo $file): int => $file->getMTime())
            ->values();

        $backups->slice($keep)->each(fn (\SplFileInfo $file): bool => File::delete($file->getPathname()));
    }
}
