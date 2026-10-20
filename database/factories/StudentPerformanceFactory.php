<?php

namespace Database\Factories;

use App\Models\Student;
use App\Models\StudentPerformance;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StudentPerformance>
 */
class StudentPerformanceFactory extends Factory
{
    protected $model = StudentPerformance::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'student_id' => Student::factory(),

            'resting_heart_rate' => fake()->numberBetween(55, 75),
            'max_heart_rate' => fake()->numberBetween(185, 200),
            'training_heart_rate' => fake()->numberBetween(130, 170),

            'height' => fake()->randomFloat(2, 160, 185),
            'weight' => fake()->randomFloat(2, 55, 85),

            'sit_and_reach' => fake()->randomFloat(2, 10, 35),
            'push_up_1_min' => fake()->numberBetween(25, 55),
            'sit_up_2_min' => fake()->numberBetween(35, 70),
            'core_stability_level' => fake()->numberBetween(5, 12),

            'triple_hop_right' => fake()->randomFloat(2, 5.0, 8.5),
            'triple_hop_left' => fake()->randomFloat(2, 5.0, 8.5),
            'illinois_agility' => fake()->randomFloat(2, 14.5, 18.5),

            'rast_average_power' => fake()->randomFloat(2, 300, 600),
            'rast_fatigue_index' => fake()->randomFloat(2, 3.5, 12.0),
            'beep_test_vo2max' => fake()->randomFloat(2, 35.0, 58.0),
        ];
    }
}
