<?php

namespace Database\Factories;

use App\Enums\PostStatus;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    protected $model = Post::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->unique()->sentence(6);
        $categories = ['Latihan', 'Kejuaraan', 'Tips', 'Kegiatan'];

        return [
            'title' => $title,
            'slug' => Str::slug($title).'-'.$this->faker->unique()->numberBetween(1, 99999),
            'excerpt' => $this->faker->paragraph(),
            'content' => '<p>'.$this->faker->paragraph().'</p><p>'.$this->faker->paragraph().'</p>',
            'cover_image' => 'images/post-'.$this->faker->numberBetween(1, 6).'.jpg',
            'category' => $this->faker->randomElement($categories),
            'author_id' => User::factory(),
            'status' => PostStatus::Published,
            'published_at' => now()->subDays($this->faker->numberBetween(0, 60)),
            'is_featured' => false,
            'views' => $this->faker->numberBetween(0, 5000),
        ];
    }

    public function draft(): static
    {
        return $this->state(fn () => [
            'status' => PostStatus::Draft,
            'published_at' => null,
        ]);
    }

    public function featured(): static
    {
        return $this->state(fn () => ['is_featured' => true]);
    }
}
