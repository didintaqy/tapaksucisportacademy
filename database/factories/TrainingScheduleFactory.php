<?php

namespace Database\Factories;

use App\Models\TrainingSchedule;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TrainingSchedule>
 */
class TrainingScheduleFactory extends Factory
{
    protected $model = TrainingSchedule::class;

    public function definition(): array
    {
        return [
            'class_name' => fake()->randomElement(['Kelas Pemula', 'Kelas Lanjutan', 'Kelas Anak']),
            'branch' => fake()->randomElement(['Cabang Pusat', 'Cabang Batam Kota']),
            'day_of_week' => fake()->numberBetween(1, 7),
            'start_time' => '16:00',
            'end_time' => '18:00',
            'location' => fake()->streetAddress(),
            'coach_name' => fake()->name(),
            'is_active' => true,
            'sort_order' => fake()->numberBetween(0, 20),
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => [
            'is_active' => false,
        ]);
    }
}
