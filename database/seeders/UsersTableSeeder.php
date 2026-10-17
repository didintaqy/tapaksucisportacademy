<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = new User;
        $user->name = 'Admin';
        $user->email = 'didintaqy@gmail.com';
        $user->password = bcrypt('adminadmin');
        $user->save();

        $this->command->info('Admin user created successfully.');
    }
}
