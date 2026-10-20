<?php

namespace Database\Factories;

use App\Models\ContactMessage;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContactMessageFactory extends Factory
{
    protected $model = ContactMessage::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'message' => $this->faker->paragraphs(3, true),
            'status' => $this->faker->randomElement(['unread', 'read', 'replied']),
            'read_at' => null,
            'replied_at' => null,
            'read_by' => null,
            'replied_by' => null,
        ];
    }

    public function unread(): static
    {
        return $this->state(fn () => ['status' => 'unread']);
    }

    public function read(): static
    {
        return $this->state(fn () => ['status' => 'read', 'read_at' => now()]);
    }

    public function replied(): static
    {
        return $this->state(fn () => ['status' => 'replied', 'replied_at' => now()]);
    }
}
