<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;


class StudentPerformancesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Mengambil semua student yang sudah ada dan membuat 1-2 performa untuk masing-masing
        Student::all()->each(function ($student) {
            UserPerformance::factory()
                ->count(rand(1, 2))
                ->state(['student_id' => $student->id])
                ->create();
        });

        $this->command->info('Student performances seeded successfully.');
    }
}
