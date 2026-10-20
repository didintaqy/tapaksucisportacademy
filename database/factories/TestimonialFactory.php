<?php

namespace Database\Factories;

use App\Enums\TestimonialStatus;
use App\Models\Testimonial;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Testimonial>
 */
class TestimonialFactory extends Factory
{
    protected $model = Testimonial::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'relation' => fake()->randomElement(['Orang Tua Siswa', 'Siswa', 'Alumni']),
            'content' => fake()->paragraph(),
            'rating' => fake()->numberBetween(4, 5),
            'status' => TestimonialStatus::Pending,
            'is_featured' => false,
            'published_at' => null,
        ];
    }

    public function approved(): static
    {
        return $this->state(fn () => [
            'status' => TestimonialStatus::Approved,
            'published_at' => now(),
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn () => [
            'status' => TestimonialStatus::Rejected,
        ]);
    }
}
