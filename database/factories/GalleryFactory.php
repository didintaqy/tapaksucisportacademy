<?php

namespace Database\Factories;

use App\Models\Gallery;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Gallery>
 */
class GalleryFactory extends Factory
{
    protected $model = Gallery::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = ['Latihan Rutin', 'Kejuaraan', 'Ujian Tingkat', 'Kegiatan Sosial'];

        return [
            'title' => 'Dokumentasi '.$this->faker->randomElement($categories),
            'category' => $this->faker->randomElement($categories),
            'description' => $this->faker->sentence(),
            'image' => 'images/gallery-'.$this->faker->numberBetween(1, 9).'.jpg',
            'taken_at' => $this->faker->dateTimeBetween('-2 years')->format('Y-m-d'),
            'is_published' => true,
            'sort_order' => 0,
        ];
    }

    public function unpublished(): static
    {
        return $this->state(fn () => ['is_published' => false]);
    }
}
