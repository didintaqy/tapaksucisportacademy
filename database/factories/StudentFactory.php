<?php

namespace Database\Factories;

use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Student>
 */
class StudentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'full_name' => fake()->name(),
            'nickname' => fake()->firstName(),
            'birth_place' => fake()->city(),
            'birth_date' => fake()->date(),
            'nik' => fake()->unique()->numerify('################'),
            'family_card_number' => fake()->numerify('################'),
            'phone_number' => fake()->numerify('08##########'),
            'gender' => fake()->randomElement(['L', 'P']),
            'belt_level' => 'KUNING DASAR',
            'address' => fake()->address(),
            'education' => fake()->randomElement(['SD', 'SMP', 'SMA']),
            'photo' => null,
        ];
    }
}
