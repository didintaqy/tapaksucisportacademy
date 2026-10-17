<?php

namespace Database\Factories;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Models\PaymentType;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'student_id' => Student::factory(),
            'payment_type_id' => PaymentType::factory(),
            'period' => null,
            'description' => null,
            'amount' => $this->faker->numberBetween(50, 500) * 1000,
            'status' => PaymentStatus::Unpaid,
            'payment_method' => null,
            'due_date' => now()->addWeeks(2),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn () => [
            'status' => PaymentStatus::Pending,
            'payment_method' => PaymentMethod::Qris,
        ]);
    }

    public function paid(): static
    {
        return $this->state(fn () => [
            'status' => PaymentStatus::Paid,
            'payment_method' => PaymentMethod::Cash,
            'paid_at' => now(),
        ]);
    }
}
