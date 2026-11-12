<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class ProductionSeeder extends Seeder
{
    /**
     * Seed data minimum yang aman untuk produksi.
     *
     * Tidak membuat akun demo, siswa contoh, atau konten placeholder.
     * Jalankan hanya saat instalasi pertama:
     *
     * php artisan db:seed --class=ProductionSeeder --force
     */
    public function run(): void
    {
        $email = env('ADMIN_EMAIL');
        $password = env('ADMIN_PASSWORD');

        if (blank($email) || blank($password)) {
            throw new RuntimeException(
                'ADMIN_EMAIL dan ADMIN_PASSWORD wajib diisi di .env sebelum menjalankan ProductionSeeder.',
            );
        }

        $admin = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => env('ADMIN_NAME', 'Administrator'),
                'password' => Hash::make($password),
                'role' => UserRole::Admin,
            ],
        );

        if (! $admin->isAdmin()) {
            $admin->update(['role' => UserRole::Admin]);
        }

        $this->command->info("Admin produksi siap: {$admin->email}");

        // Jenis pembayaran adalah master data yang dibutuhkan modul keuangan.
        $this->call(PaymentTypeSeeder::class);

        // Pengaturan situs awal; admin melengkapi data resmi lewat panel.
        $this->call(SiteSettingSeeder::class);

        // Master data komponen kebugaran, item tes, kategori skor, dan benchmark usia/gender.
        $this->call(FitnessBenchmarkSeeder::class);
    }
}
