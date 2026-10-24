<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class DatabaseBackupTest extends TestCase
{
    public function test_db_backup_command_creates_sqlite_backup_file(): void
    {
        $directory = sys_get_temp_dir().DIRECTORY_SEPARATOR.'ts-backup-'.uniqid();

        File::ensureDirectoryExists($directory);

        $this->artisan('db:backup', ['--path' => $directory])
            ->assertSuccessful();

        $backups = glob($directory.DIRECTORY_SEPARATOR.'database-*');

        $this->assertNotEmpty($backups);

        foreach ($backups as $backup) {
            File::delete($backup);
        }

        File::deleteDirectory($directory);
    }
}
