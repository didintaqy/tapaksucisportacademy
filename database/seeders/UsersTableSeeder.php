<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Akun demo untuk development/testing.
 *
 * JANGAN dipakai di produksi: seeder ini membuat akun dengan kata sandi
 * publik dan menimpa kata sandi akun yang emailnya sama. Produksi memakai
 * ProductionSeeder.
 */
class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminEmail = env('ADMIN_EMAIL', 'admin@tapaksuci.test');
        $adminPassword = env('ADMIN_PASSWORD', 'password');

        // firstOrCreate agar kata sandi akun yang sudah ada tidak ditimpa
        // setiap kali seeder dijalankan ulang.
        $user = User::firstOrCreate(
            ['email' => $adminEmail],
            [
                'name' => env('ADMIN_NAME', 'Admin'),
                'password' => Hash::make($adminPassword),
                'role' => UserRole::Admin,
            ],
        );

        $this->command?->info("Admin user ready: {$user->email}");

        $users = [
            [
                'name' => 'Operator Pusat',
                'email' => 'operator.pusat@tapaksuci.test',
                'password' => Hash::make('password'),
                'role' => UserRole::Operator,
            ],
            [
                'name' => 'Operator Batam',
                'email' => 'operator.batam@tapaksuci.test',
                'password' => Hash::make('password'),
                'role' => UserRole::Operator,
            ],
            [
                'name' => 'Pelatih Budi Santoso',
                'email' => 'pelatih.budi@tapaksuci.test',
                'password' => Hash::make('password'),
                'role' => UserRole::Pelatih,
            ],
            [
                'name' => 'Pelatih Siti Rahayu',
                'email' => 'pelatih.siti@tapaksuci.test',
                'password' => Hash::make('password'),
                'role' => UserRole::Pelatih,
            ],
            [
                'name' => 'Pelatih Ahmad Wijaya',
                'email' => 'pelatih.ahmad@tapaksuci.test',
                'password' => Hash::make('password'),
                'role' => UserRole::Pelatih,
            ],
        ];

        foreach ($users as $attributes) {
            $created = User::firstOrCreate(
                ['email' => $attributes['email']],
                $attributes,
            );

            $this->command?->info("Demo user ready: {$created->email}");
        }
    }
}
