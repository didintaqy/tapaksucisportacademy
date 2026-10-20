<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(UsersTableSeeder::class);
        $this->call(StudentsTableSeeder::class);
        $this->call(StudentPerformanceTableSeeder::class);
        $this->call(PaymentTypeSeeder::class);
        $this->call(PaymentsTableSeeder::class);
        $this->call(GallerySeeder::class);
        $this->call(PostSeeder::class);
        $this->call(TestimonialSeeder::class);
        $this->call(FaqSeeder::class);
        $this->call(TrainingScheduleSeeder::class);
        $this->call(StudentRegistrationSeeder::class);
        $this->call(EventSeeder::class);
        $this->call(AttendanceSeeder::class);
    }
}
