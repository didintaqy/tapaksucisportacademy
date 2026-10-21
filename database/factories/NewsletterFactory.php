<?php

namespace Database\Factories;

use App\Models\Newsletter;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class NewsletterFactory extends Factory
{
    protected $model = Newsletter::class;

    public function definition(): array
    {
        return [
            'subject' => $this->faker->sentence(),
            'content' => '<p>'.$this->faker->paragraphs(3, true).'</p>',
            'status' => 'draft',
            'sent_at' => null,
            'recipients_count' => 0,
            'created_by' => User::factory(),
        ];
    }

    public function sent(): static
    {
        return $this->state(fn () => [
            'status' => 'sent',
            'sent_at' => now(),
            'recipients_count' => $this->faker->numberBetween(10, 500),
        ]);
    }
}
