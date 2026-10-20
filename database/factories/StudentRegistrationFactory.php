<?php

namespace Database\Factories;

use App\Enums\RegistrationStatus;
use App\Models\StudentRegistration;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StudentRegistration>
 */
class StudentRegistrationFactory extends Factory
{
    protected $model = StudentRegistration::class;

    public function definition(): array
    {
        return [
            'full_name' => fake()->name(),
            'nickname' => fake()->firstName(),
            'gender' => fake()->randomElement(['L', 'P']),
            'birth_place' => fake()->city(),
            'birth_date' => fake()->dateTimeBetween('-17 years', '-5 years')->format('Y-m-d'),
            'education' => fake()->randomElement(['SD', 'SMP', 'SMA']),
            'parent_name' => fake()->name(),
            'phone' => fake()->phoneNumber(),
            'email' => fake()->safeEmail(),
            'address' => fake()->address(),
            'branch' => fake()->randomElement(['Cabang Pusat', 'Cabang Batam Kota']),
            'notes' => fake()->optional()->sentence(),
            'status' => RegistrationStatus::New,
            'admin_notes' => null,
        ];
    }

    public function accepted(): static
    {
        return $this->state(fn () => [
            'status' => RegistrationStatus::Accepted,
        ]);
    }
}
