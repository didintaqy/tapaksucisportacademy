<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class DatabaseBackupTest extends TestCase
{
    private string $directory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->directory = sys_get_temp_dir().DIRECTORY_SEPARATOR.'ts-backup-'.uniqid();
        File::ensureDirectoryExists($this->directory);
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->directory);

        parent::tearDown();
    }

    public function test_backup_creates_file_and_checksum(): void
    {
        $this->artisan('db:backup', ['--path' => $this->directory])->assertSuccessful();

        $files = File::files($this->directory);
        $backups = collect($files)->filter(fn ($f) => str_starts_with($f->getFilename(), 'database-')
            && ! str_ends_with($f->getFilename(), '.sha256'));

        $this->assertCount(1, $backups);
        $this->assertTrue(File::exists($backups->first()->getPathname().'.sha256'));
        $this->assertGreaterThan(0, $backups->first()->getSize());
    }

    public function test_backup_supports_uncompressed_output(): void
    {
        $this->artisan('db:backup', ['--path' => $this->directory, '--no-compress' => true])
            ->assertSuccessful();

        $backup = collect(File::files($this->directory))
            ->first(fn ($f) => str_ends_with($f->getFilename(), '.sqlite'));

        $this->assertNotNull($backup);
        $this->assertStringStartsWith('SQLite format 3', File::get($backup->getPathname()));
    }

    public function test_backup_prunes_old_files_and_their_checksums(): void
    {
        for ($i = 5; $i >= 1; $i--) {
            $name = $this->directory.'/database-2026010'.$i.'_000000.sqlite.gz';
            File::put($name, 'x');
            File::put($name.'.sha256', 'y');
            touch($name, time() - ($i * 60));
        }

        $this->artisan('db:backup', ['--path' => $this->directory, '--keep' => 3])
            ->assertSuccessful();

        $backups = collect(File::files($this->directory))
            ->filter(fn ($f) => str_starts_with($f->getFilename(), 'database-')
                && ! str_ends_with($f->getFilename(), '.sha256'));

        $this->assertLessThanOrEqual(3, $backups->count());

        foreach ($backups as $backup) {
            $this->assertTrue(
                File::exists($backup->getPathname().'.sha256'),
                'Checksum untuk backup yang dipertahankan harus tetap ada.',
            );
        }
    }

    public function test_verify_accepts_intact_backup(): void
    {
        $this->artisan('db:backup', ['--path' => $this->directory])->assertSuccessful();

        $backup = collect(File::files($this->directory))
            ->first(fn ($f) => ! str_ends_with($f->getFilename(), '.sha256'));

        $this->artisan('db:restore', ['file' => $backup->getPathname(), '--verify' => true])
            ->expectsOutputToContain('Checksum valid.')
            ->assertSuccessful();
    }

    public function test_verify_rejects_tampered_backup(): void
    {
        $this->artisan('db:backup', ['--path' => $this->directory])->assertSuccessful();

        $backup = collect(File::files($this->directory))
            ->first(fn ($f) => ! str_ends_with($f->getFilename(), '.sha256'));

        File::append($backup->getPathname(), 'corruption');

        $this->artisan('db:restore', ['file' => $backup->getPathname(), '--verify' => true])
            ->expectsOutputToContain('Checksum tidak cocok')
            ->assertFailed();
    }

    public function test_verify_rejects_backup_without_checksum(): void
    {
        $file = $this->directory.'/database-20260101_000000.sqlite';
        File::put($file, 'SQLite format 3');

        $this->artisan('db:restore', ['file' => $file, '--verify' => true])
            ->expectsOutputToContain('Checksum tidak ditemukan')
            ->assertFailed();
    }

    /**
     * Menguji jalur MySQL tanpa server MySQL: sebuah skrip palsu dipakai
     * sebagai `mysqldump` untuk memastikan argumen dan aliran data benar.
     */
    public function test_mysql_backup_passes_tables_to_dump_and_compresses_output(): void
    {
        $bin = $this->directory.'/bin';
        File::ensureDirectoryExists($bin);

        // Process::setEnv() hanya menimpa env proses anak; dump palsu menulis
        // argumennya ke path tetap agar dapat diperiksa setelah perintah selesai.
        $fakeDump = $bin.'/mysqldump';
        File::put($fakeDump, <<<'SH'
#!/bin/sh
# Tulis argumen agar bisa diperiksa, lalu keluarkan dump palsu.
{
    printf '%s\n' "$@"
    echo "MYSQL_PWD=${MYSQL_PWD:-}"
} > /tmp/ts-args.txt
echo "-- dump"
echo "CREATE TABLE users (id int);"
echo "CREATE TABLE payments (id int);"
SH);
        chmod($fakeDump, 0755);

        $originalPath = getenv('PATH');
        putenv('PATH='.$bin.':'.$originalPath);

        config([
            'database.default' => 'mysql',
            'database.connections.mysql' => [
                'driver' => 'mysql',
                'host' => '127.0.0.1',
                'port' => '3306',
                'database' => 'tapaksuci',
                'username' => 'u607306241',
                'password' => 'rahasia-db',
            ],
        ]);

        try {
            $this->artisan('db:backup', ['--path' => $this->directory])->assertSuccessful();
        } finally {
            putenv('PATH='.$originalPath);
        }

        $args = File::get('/tmp/ts-args.txt');
        File::delete('/tmp/ts-args.txt');

        $this->assertStringContainsString('--single-transaction', $args);
        $this->assertStringContainsString('tapaksuci', $args);
        $this->assertStringContainsString('u607306241', $args);
        $this->assertStringContainsString('MYSQL_PWD=rahasia-db', $args);
        $this->assertStringNotContainsString('--password', $args);

        $backup = collect(File::files($this->directory))
            ->first(fn ($f) => str_ends_with($f->getFilename(), '.sql.gz'));

        $this->assertNotNull($backup, 'Backup MySQL harus berekstensi .sql.gz');
        $this->assertStringContainsString('CREATE TABLE users', gzdecode(File::get($backup->getPathname())));

        // Checksum dihitung dari isi yang sudah didekompresi.
        $expected = trim(explode(' ', File::get($backup->getPathname().'.sha256'))[0]);
        $this->assertSame(hash('sha256', gzdecode(File::get($backup->getPathname()))), $expected);
    }

    public function test_mysql_restore_pipes_dump_into_mysql_client(): void
    {
        $bin = $this->directory.'/bin';
        File::ensureDirectoryExists($bin);

        $fakeMysql = $bin.'/mysql';
        File::put($fakeMysql, <<<'SH'
#!/bin/sh
{ printf '%s\n' "$@"; cat; } > /tmp/ts-restore.txt
SH);
        chmod($fakeMysql, 0755);

        $originalPath = getenv('PATH');
        putenv('PATH='.$bin.':'.$originalPath);

        config([
            'database.default' => 'mysql',
            'database.connections.mysql' => [
                'host' => '127.0.0.1',
                'port' => '3306',
                'database' => 'tapaksuci',
                'username' => 'u607306241',
                'password' => 'rahasia-db',
            ],
        ]);

        $dump = $this->directory.'/database-test.sql';
        $sql = "CREATE TABLE users (id int);\nINSERT INTO users VALUES (1);\n";
        File::put($dump, $sql);
        File::put($dump.'.sha256', hash('sha256', $sql).'  database-test.sql'.PHP_EOL);

        try {
            $this->artisan('db:restore', ['file' => $dump, '--force' => true])
                ->expectsOutputToContain('Checksum valid.')
                ->assertSuccessful();
        } finally {
            putenv('PATH='.$originalPath);
        }

        $captured = File::get('/tmp/ts-restore.txt');
        File::delete('/tmp/ts-restore.txt');

        $this->assertStringContainsString('tapaksuci', $captured);
        $this->assertStringContainsString('INSERT INTO users VALUES (1);', $captured);
    }

    public function test_mysql_backup_fails_cleanly_when_mysqldump_missing(): void
    {
        putenv('PATH=/nonexistent');

        config([
            'database.default' => 'mysql',
            'database.connections.mysql' => [
                'host' => '127.0.0.1',
                'port' => '3306',
                'database' => 'tapaksuci',
                'username' => 'root',
                'password' => '',
            ],
        ]);

        try {
            $this->artisan('db:backup', ['--path' => $this->directory])->assertFailed();
        } finally {
            putenv('PATH=/usr/local/bin:/usr/bin:/bin');
        }

        $this->assertEmpty(glob($this->directory.'/database-*'), 'Backup gagal tidak boleh menyisakan file.');
    }
}
