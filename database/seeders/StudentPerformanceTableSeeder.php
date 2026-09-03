<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\StudentPerformance;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class StudentPerformanceTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $students = Student::all();

        if ($students->isEmpty()) {
            $this->command->warn('Tabel students masih kosong. Jalankan StudentSeeder terlebih dahulu.');
            return;
        }

        foreach ($students as $student) {
            // Asumsikan tes dimulai dari beberapa bulan ke belakang
            $baseDate = Carbon::now()->subMonths(5);

            for ($i = 1; $i <= 5; $i++) {
                // Parameter fisik bertahap meningkat/bervariasi seiring latihan
                $testDate = (clone $baseDate)->addMonths($i - 1)->addDays(rand(1, 5));

                StudentPerformance::create([
                    'student_id' => $student->id,

                    // Heart Rate (bpm)
                    'resting_heart_rate'   => rand(65, 85),
                    'max_heart_rate'       => rand(195, 205),
                    'training_heart_rate'  => rand(150, 175),

                    // Anthropometry
                    'height'               => round(rand(145, 175) / 100, 2), // meter (misal: 1.55 - 1.75)
                    'weight'               => round(rand(45, 70) + (rand(0, 9) / 10), 2), // kg

                    // Flexibility (cm)
                    'sit_and_reach'        => round(rand(15, 30) + (rand(0, 9) / 10), 2),

                    // Muscular Endurance (reps)
                    'push_up_1_min'        => rand(15, 35),
                    'sit_up_2_min'         => rand(30, 60),

                    // Core Stability
                    'core_stability_level' => (string) rand(1, 12),

                    // Triple Hop Test (meter)
                    'triple_hop_right'     => round(rand(30, 48) / 10, 2), // misal: 3.00 - 4.80 m
                    'triple_hop_left'      => round(rand(30, 48) / 10, 2),

                    // Illinois Agility Test (detik - semakin kecil semakin lincah)
                    'illinois_agility'     => round(rand(170, 230) / 10, 2), // 17.0 - 23.0 dtk

                    // RAST Test
                    'rast_average_power'   => round(rand(220, 500) + (rand(0, 99) / 100), 2), // Watt
                    'rast_fatigue_index'   => round(rand(30, 75) / 10, 2), // Watt

                    // Beep Test VO2Max (ml/kg/min)
                    'beep_test_vo2max'     => round(rand(35, 55) + (rand(0, 99) / 100), 2),

                    'created_at'           => $testDate,
                    'updated_at'           => $testDate,
                ]);
            }
        }

        $this->command->info('Berhasil membuat 5 data riwayat performa untuk setiap siswa.');
    }
}