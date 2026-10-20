<?php

namespace Database\Factories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventFactory extends Factory
{
    protected $model = Event::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->optional()->paragraph(),
            'starts_at' => $this->faker->dateTimeBetween('now', '+3 months'),
            'ends_at' => $this->faker->optional()->dateTimeBetween('+1 day', '+5 days'),
            'type' => $this->faker->randomElement(['latihan', 'kejuaraan', 'ukt', 'pengumuman', 'libur']),
            'is_public' => true,
            'created_by' => null,
        ];
    }

    public function public(): static
    {
        return $this->state(fn () => ['is_public' => true]);
    }

    public function private(): static
    {
        return $this->state(fn () => ['is_public' => false]);
    }

    public function type(string $type): static
    {
        return $this->state(fn () => ['type' => $type]);
    }

    public function thisMonth(): static
    {
        return $this->state(fn () => [
            'starts_at' => now()->startOfMonth()->addDays($this->faker->numberBetween(1, 25))->setTime(10, 0),
        ]);
    }

    public function nextMonth(): static
    {
        return $this->state(fn () => [
            'starts_at' => now()->addMonth()->startOfMonth()->addDays($this->faker->numberBetween(1, 25))->setTime(10, 0),
        ]);
    }

    public function lastMonth(): static
    {
        return $this->state(fn () => [
            'starts_at' => now()->subMonth()->startOfMonth()->addDays($this->faker->numberBetween(1, 25))->setTime(10, 0),
        ]);
    }
}
