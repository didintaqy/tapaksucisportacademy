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
    }
}
