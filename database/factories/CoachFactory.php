<?php

namespace Database\Factories;

use App\Models\Coach;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Coach>
 */
class CoachFactory extends Factory
{
    protected $model = Coach::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'title' => fake()->randomElement(['Pelatih Kepala', 'Asisten Pelatih', 'Pelatih Prestasi']),
            'specialization' => fake()->randomElement(['Teknik Dasar', 'Seni Tunggal', 'Tanding', 'Kebugaran']),
            'bio' => fake()->paragraph(),
            'photo_path' => null,
            'experience_years' => fake()->numberBetween(1, 20),
            'sort_order' => 0,
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (): array => ['is_active' => false]);
    }
}
