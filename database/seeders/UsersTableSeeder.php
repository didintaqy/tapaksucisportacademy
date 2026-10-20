<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::updateOrCreate(
            ['email' => env('ADMIN_EMAIL', 'didintaqy@gmail.com')],
            [
                'name' => env('ADMIN_NAME', 'Admin'),
                'password' => Hash::make(env('ADMIN_PASSWORD', 'adminadmin')),
                'role' => UserRole::Admin,
            ],
        );

        $this->command->info("Admin user ready: {$user->email}");

        // Create operator users
        $operators = [
            [
                'name' => 'Operator Pusat',
                'email' => 'operator.pusat@tapaksuci.ac.id',
                'password' => Hash::make('operator123'),
                'role' => UserRole::Operator,
            ],
            [
                'name' => 'Operator Batam',
                'email' => 'operator.batam@tapaksuci.ac.id',
                'password' => Hash::make('operator123'),
                'role' => UserRole::Operator,
            ],
        ];

        foreach ($operators as $operator) {
            $op = User::updateOrCreate(['email' => $operator['email']], $operator);
            $this->command->info("Operator user ready: {$op->email}");
        }

        // Create pelatih (coach) users
        $coaches = [
            [
                'name' => 'Pelatih Budi Santoso',
                'email' => 'pelatih.budi@tapaksuci.ac.id',
                'password' => Hash::make('pelatih123'),
                'role' => UserRole::Pelatih,
            ],
            [
                'name' => 'Pelatih Siti Rahayu',
                'email' => 'pelatih.siti@tapaksuci.ac.id',
                'password' => Hash::make('pelatih123'),
                'role' => UserRole::Pelatih,
            ],
            [
                'name' => 'Pelatih Ahmad Wijaya',
                'email' => 'pelatih.ahmad@tapaksuci.ac.id',
                'password' => Hash::make('pelatih123'),
                'role' => UserRole::Pelatih,
            ],
        ];

        foreach ($coaches as $coach) {
            $c = User::updateOrCreate(['email' => $coach['email']], $coach);
            $this->command->info("Pelatih user ready: {$c->email}");
        }
    }
}
