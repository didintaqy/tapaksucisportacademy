<?php

namespace Database\Factories;

use App\Models\Student;
use App\Models\UserPerformance;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserPerformance>
 */
class UserPerformanceFactory extends Factory
{
    protected $model = UserPerformance::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // Mengambil Student ID secara acak jika tidak didefinisikan saat dipanggil
            'student_id' => Student::factory(),

            // Denyut Nadi (bpm) - Rata-rata dewasa muda usia ~24 tahun
            'resting_heart_rate' => fake()->numberBetween(55, 75),
            'max_heart_rate' => fake()->numberBetween(185, 200), // Est. Max HR = 220 - 24
            'training_heart_rate' => fake()->numberBetween(130, 170),

            // Antropometri (Usia 24)
            'height' => fake()->randomFloat(2, 160, 185), // cm
            'weight' => fake()->randomFloat(2, 55, 85),   // kg

            // Tes Fisik & Kelenturan
            'sit_and_reach' => fake()->randomFloat(2, 10, 35),  // cm
            'push_up_1_min' => fake()->numberBetween(25, 55),    // reps
            'sit_up_2_min' => fake()->numberBetween(35, 70),    // reps
            'core_stability_level' => fake()->numberBetween(5, 12),     // Level 1-12

            // Daya Ledak & Kelincahan
            'triple_hop_right' => fake()->randomFloat(2, 5.0, 8.5),   // meter
            'triple_hop_left' => fake()->randomFloat(2, 5.0, 8.5),   // meter
            'illinois_agility' => fake()->randomFloat(2, 14.5, 18.5), // detik

            // RAST Test & VO2Max
            'rast_average_power' => fake()->randomFloat(2, 300, 600), // Watt
            'rast_fatigue_index' => fake()->randomFloat(2, 3.5, 12.0),
            'beep_test_vo2max' => fake()->randomFloat(2, 35.0, 58.0), // ml/kg/min
        ];
    }
}
