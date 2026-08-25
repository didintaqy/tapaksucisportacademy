<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = new \App\Models\User();
        $user->name = 'Admin';
        $user->email = 'didintaqy@gmail.com';
        $user->password = bcrypt('adminadmin');
        $user->save();

        $this->command->info('Admin user created successfully.');
    }
}
