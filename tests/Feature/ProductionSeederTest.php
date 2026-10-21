<?php

namespace Tests\Feature;

use App\Models\PaymentType;
use App\Models\User;
use Database\Seeders\ProductionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductionSeederTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setEnv('ADMIN_EMAIL', 'production-admin@example.com');
        $this->setEnv('ADMIN_PASSWORD', 'strong-production-password');
        $this->setEnv('ADMIN_NAME', 'Production Admin');
    }

    protected function tearDown(): void
    {
        $this->setEnv('ADMIN_EMAIL', null);
        $this->setEnv('ADMIN_PASSWORD', null);
        $this->setEnv('ADMIN_NAME', null);

        parent::tearDown();
    }

    public function test_production_seeder_creates_only_admin_and_payment_types(): void
    {
        $this->seed(ProductionSeeder::class);

        $this->assertDatabaseHas('users', [
            'email' => 'production-admin@example.com',
            'role' => 'admin',
        ]);

        $this->assertSame(1, User::count());
        $this->assertGreaterThan(0, PaymentType::count());
    }

    private function setEnv(string $key, ?string $value): void
    {
        if ($value === null) {
            putenv($key);
            unset($_ENV[$key], $_SERVER[$key]);

            return;
        }

        putenv("{$key}={$value}");
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
    }
}
