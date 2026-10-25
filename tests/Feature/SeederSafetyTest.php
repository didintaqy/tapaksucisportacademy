<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Database\Seeders\UsersTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SeederSafetyTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Deploy produksi tidak boleh menjalankan seeder data demo.
     */
    public function test_deploy_workflow_does_not_run_database_seeder(): void
    {
        $workflow = file_get_contents(base_path('.github/workflows/deploy.yml'));

        $this->assertStringNotContainsString(
            'artisan db:seed --force',
            $workflow,
            'Deploy tidak boleh menjalankan db:seed: DatabaseSeeder membuat data demo.',
        );

        $this->assertStringNotContainsString('artisan db:seed --class=DatabaseSeeder', $workflow);
    }

    /**
     * `db:seed` tanpa --class di produksi hanya menjalankan ProductionSeeder.
     */
    public function test_database_seeder_skips_demo_data_in_production(): void
    {
        $this->app->detectEnvironment(fn (): string => 'production');

        putenv('ADMIN_EMAIL=admin@example.test');
        putenv('ADMIN_PASSWORD=rahasia-sekali');

        $this->artisan('db:seed', ['--force' => true])->assertExitCode(0);

        $this->assertDatabaseHas('users', [
            'email' => 'admin@example.test',
            'role' => UserRole::Admin->value,
        ]);

        $this->assertDatabaseMissing('users', ['email' => 'operator.pusat@tapaksuci.test']);
        $this->assertDatabaseMissing('users', ['email' => 'pelatih.budi@tapaksuci.test']);
        $this->assertSame(1, User::query()->count());
    }

    /**
     * Seeder demo tidak menimpa kata sandi akun yang sudah ada.
     */
    public function test_demo_seeder_does_not_overwrite_existing_passwords(): void
    {
        putenv('ADMIN_EMAIL=admin@tapaksuci.test');
        putenv('ADMIN_PASSWORD=kata-sandi-baru');

        $admin = User::factory()->create([
            'email' => 'admin@tapaksuci.test',
            'password' => Hash::make('kata-sandi-lama'),
            'role' => UserRole::Admin,
        ]);

        $this->seed(UsersTableSeeder::class);

        $this->assertTrue(
            Hash::check('kata-sandi-lama', $admin->refresh()->password),
            'Kata sandi admin yang sudah ada tidak boleh ditimpa seeder demo.',
        );
    }

    /**
     * Akun demo tidak memakai kredensial yang dapat ditebak di produksi.
     */
    public function test_demo_seeder_has_no_hardcoded_production_domains(): void
    {
        $source = file_get_contents(database_path('seeders/UsersTableSeeder.php'));

        $this->assertStringNotContainsString('operator123', $source);
        $this->assertStringNotContainsString('pelatih123', $source);
        $this->assertStringNotContainsString('adminadmin', $source);
        $this->assertStringNotContainsString('@tapaksuci.ac.id', $source);
    }
}
