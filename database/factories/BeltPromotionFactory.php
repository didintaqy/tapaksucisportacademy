<?php

namespace Database\Factories;

use App\Enums\PromotionResult;
use App\Models\BeltPromotion;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BeltPromotionFactory extends Factory
{
    protected $model = BeltPromotion::class;

    public function definition(): array
    {
        $belts = ['putih', 'kuning', 'hijau', 'biru', 'merah', 'coklat', 'hitam_1'];
        $currentBelt = $this->faker->randomElement($belts);
        $currentIndex = array_search($currentBelt, $belts);
        $targetBelt = $currentIndex !== false && $currentIndex < count($belts) - 1
            ? $belts[$currentIndex + 1]
            : $currentBelt;

        return [
            'student_id' => Student::factory(),
            'examined_by' => User::factory()->state(['role' => 'pelatih']),
            'exam_date' => $this->faker->dateTimeBetween('-6 months', 'now')->format('Y-m-d'),
            'current_belt' => $currentBelt,
            'target_belt' => $targetBelt,
            'result' => $this->faker->randomElement([PromotionResult::Lulus, PromotionResult::TidakLulus, PromotionResult::Ditunda]),
            'notes' => $this->faker->optional()->paragraph(),
            'examiner_notes' => $this->faker->optional()->sentence(),
            'certificate_issued_at' => null,
            'certificate_number' => null,
        ];
    }

    public function lulus(): static
    {
        return $this->state(fn () => [
            'result' => PromotionResult::Lulus,
            'certificate_issued_at' => now(),
            'certificate_number' => 'UKT-'.now()->year.'-'.str_pad(random_int(1, 9999), 4, '0', STR_PAD_LEFT),
        ]);
    }

    public function tidakLulus(): static
    {
        return $this->state(fn () => ['result' => PromotionResult::TidakLulus]);
    }

    public function ditunda(): static
    {
        return $this->state(fn () => ['result' => PromotionResult::Ditunda]);
    }

    public function forStudent(Student $student): static
    {
        return $this->state(fn () => ['student_id' => $student->id]);
    }

    public function recent(): static
    {
        return $this->state(fn () => [
            'exam_date' => now()->subDays($this->faker->numberBetween(0, 30))->format('Y-m-d'),
        ]);
    }
}
