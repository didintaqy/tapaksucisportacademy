<?php

namespace Database\Factories;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Models\PaymentStatusHistory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PaymentStatusHistory>
 */
class PaymentStatusHistoryFactory extends Factory
{
    protected $model = PaymentStatusHistory::class;

    public function definition(): array
    {
        return [
            'payment_id' => Payment::factory(),
            'from_status' => PaymentStatus::Unpaid,
            'to_status' => PaymentStatus::Paid,
            'changed_by' => User::factory(),
            'reason' => $this->faker->sentence(4),
            'metadata' => null,
        ];
    }
}
