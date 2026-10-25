<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;
use Throwable;

class BackupDatabase extends Command
{
    protected $signature = 'db:backup
                            {--path= : Direktori tujuan backup (default: storage/app/backups)}
                            {--keep=14 : Jumlah backup terakhir yang dipertahankan}
                            {--no-compress : Simpan tanpa gzip (untuk restore cepat/uji)}';

    protected $description = 'Membuat backup database aplikasi (SQLite/MySQL) beserta checksum';

    public function handle(): int
    {
        $connection = (string) config('database.default');
        $directory = (string) ($this->option('path') ?: storage_path('app/backups'));
        $compress = ! $this->option('no-compress');

        File::ensureDirectoryExists($directory);

        $extension = $connection === 'mysql' ? 'sql' : 'sqlite';
        $filename = sprintf('database-%s.%s', now()->format('Ymd_His'), $extension);

        $target = $directory.DIRECTORY_SEPARATOR.$filename.($compress ? '.gz' : '');

        try {
            $connection === 'mysql'
                ? $this->backupMysql($target, $compress)
                : $this->backupSqlite($target);
        } catch (Throwable $exception) {
            File::delete($target);
            $this->error('Backup gagal: '.$exception->getMessage());

            return self::FAILURE;
        }

        if (! File::exists($target) || File::size($target) === 0) {
            File::delete($target);
            $this->error('Backup gagal: berkas hasil kosong.');

            return self::FAILURE;
        }

        // Checksum dibuat dari isi yang sudah didekompresi agar verifikasi
        // restore tidak bergantung pada versi gzip.
        $checksum = ($compress ? $this->gunzip($target) : File::get($target));
        File::put($target.'.sha256', hash('sha256', $checksum).'  '.basename($target).PHP_EOL);

        $this->pruneOldBackups($directory, (int) $this->option('keep'));

        $this->info(sprintf(
            'Backup database berhasil: %s (%s)',
            $target,
            $this->humanSize(File::size($target)),
        ));

        return self::SUCCESS;
    }

    /**
     * mysqldump menulis ke stdout; gzip dikerjakan oleh shell (pipe) atau
     * dilewati bila --no-compress.
     */
    private function backupMysql(string $target, bool $compress): void
    {
        $config = (array) config('database.connections.'.config('database.default'));

        $command = [
            'mysqldump',
            '--host='.(string) ($config['host'] ?? '127.0.0.1'),
            '--port='.(string) ($config['port'] ?? '3306'),
            '--user='.(string) ($config['username'] ?? 'root'),
            '--single-transaction',
            '--quick',
            '--routines',
            '--triggers',
            '--no-tablespaces',
            '--default-character-set=utf8mb4',
            (string) ($config['database'] ?? ''),
        ];

        $process = new Process($command);

        // Kata sandi lewat environment, bukan argumen, agar tidak terlihat
        // pada daftar proses (ps aux). PATH diwarisi agar mysqldump ditemukan.
        $process->setEnv(array_filter([
            'MYSQL_PWD' => (string) ($config['password'] ?? ''),
        ], fn ($value): bool => $value !== ''));

        $handle = fopen($target, 'wb');
        $gzip = $compress ? gzopen($target, 'wb6') : null;

        $process->run(function (string $type, string $buffer) use ($handle, $gzip): void {
            if ($type !== Process::OUT) {
                return;
            }

            $gzip === null ? fwrite($handle, $buffer) : gzwrite($gzip, $buffer);
        });

        fclose($handle);
        $gzip === null ?: gzclose($gzip);

        if (! $process->isSuccessful()) {
            throw new \RuntimeException(
                trim($process->getErrorOutput()) ?: 'mysqldump keluar dengan kode '.$process->getExitCode(),
            );
        }
    }

    private function backupSqlite(string $target): void
    {
        // VACUUM INTO menulis snapshot konsisten walau tidak ada sqlite3 CLI.
        $quoted = DB::connection()->getPdo()->quote($target);
        DB::connection()->statement("VACUUM INTO {$quoted}");
    }

    private function gunzip(string $path): string
    {
        $handle = gzopen($path, 'rb');
        $contents = '';

        while (! gzeof($handle)) {
            $contents .= gzread($handle, 1048576);
        }

        gzclose($handle);

        return $contents;
    }

    private function pruneOldBackups(string $directory, int $keep): void
    {
        if ($keep < 1) {
            return;
        }

        $backups = collect(File::files($directory))
            ->filter(fn (\SplFileInfo $file): bool => str_starts_with($file->getFilename(), 'database-')
                && ! str_ends_with($file->getFilename(), '.sha256'))
            ->sortByDesc(fn (\SplFileInfo $file): int => $file->getMTime())
            ->values();

        $backups->slice($keep)->each(function (\SplFileInfo $file): void {
            File::delete($file->getPathname());
            File::delete($file->getPathname().'.sha256');
        });
    }

    private function humanSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $power = $bytes > 0 ? (int) floor(log($bytes, 1024)) : 0;

        return round($bytes / (1024 ** $power), 2).' '.$units[$power];
    }
}
