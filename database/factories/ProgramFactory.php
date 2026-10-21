<?php

namespace Database\Factories;

use App\Enums\ProgramLevel;
use App\Models\Program;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Program>
 */
class ProgramFactory extends Factory
{
    protected $model = Program::class;

    public function definition(): array
    {
        $name = 'Program '.$this->faker->unique()->words(2, true);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'short_description' => $this->faker->sentence(),
            'description' => '<p>'.$this->faker->paragraph().'</p>',
            'cover_image' => 'images/programs-item-image-'.$this->faker->numberBetween(1, 8).'.png',
            'age_min' => $this->faker->numberBetween(5, 12),
            'age_max' => $this->faker->numberBetween(15, 40),
            'level' => $this->faker->randomElement(ProgramLevel::cases()),
            'session_duration_minutes' => $this->faker->randomElement([60, 75, 90]),
            'sessions_per_week' => $this->faker->numberBetween(2, 4),
            'monthly_fee' => $this->faker->randomElement([150000, 200000, 250000, 300000]),
            'branches' => ['Cabang Pusat'],
            'is_active' => true,
            'is_featured' => false,
            'sort_order' => 0,
            'meta_title' => null,
            'meta_description' => null,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }

    public function featured(): static
    {
        return $this->state(fn () => ['is_featured' => true]);
    }
}
