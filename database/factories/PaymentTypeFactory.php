<?php

namespace Database\Factories;

use App\Enums\PaymentPeriodType;
use App\Models\PaymentType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<PaymentType>
 */
class PaymentTypeFactory extends Factory
{
    protected $model = PaymentType::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->unique()->randomElement([
            'Iuran Bulanan',
            'Biaya Pendaftaran',
            'Ujian Kenaikan Tingkat',
            'Biaya Kejuaraan',
        ]);

        return [
            'name' => $name,
            'slug' => Str::slug($name).'-'.$this->faker->unique()->numberBetween(1, 99999),
            'description' => null,
            'default_amount' => $this->faker->numberBetween(50, 500) * 1000,
            'period_type' => PaymentPeriodType::OneTime,
            'is_active' => true,
            'sort_order' => 0,
        ];
    }

    public function monthly(): static
    {
        return $this->state(fn () => ['period_type' => PaymentPeriodType::Monthly]);
    }
}
