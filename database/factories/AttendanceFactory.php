<?php

namespace Database\Factories;

use App\Models\Attendance;
use App\Models\Student;
use App\Models\TrainingSchedule;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttendanceFactory extends Factory
{
    protected $model = Attendance::class;

    public function definition(): array
    {
        return [
            'student_id' => Student::factory(),
            'training_schedule_id' => TrainingSchedule::factory(),
            'date' => $this->faker->dateTimeBetween('-3 months', 'now')->format('Y-m-d'),
            'status' => $this->faker->randomElement(['hadir', 'izin', 'sakit', 'alpa']),
            'notes' => $this->faker->optional()->sentence(),
            'recorded_by' => User::factory()->state(['role' => 'pelatih']),
        ];
    }

    public function hadir(): static
    {
        return $this->state(fn () => ['status' => 'hadir']);
    }

    public function izin(): static
    {
        return $this->state(fn () => ['status' => 'izin']);
    }

    public function sakit(): static
    {
        return $this->state(fn () => ['status' => 'sakit']);
    }

    public function alpa(): static
    {
        return $this->state(fn () => ['status' => 'alpa']);
    }

    public function forSchedule(TrainingSchedule $schedule): static
    {
        return $this->state(fn () => ['training_schedule_id' => $schedule->id]);
    }

    public function forStudent(Student $student): static
    {
        return $this->state(fn () => ['student_id' => $student->id]);
    }

    public function recent(): static
    {
        return $this->state(fn () => [
            'date' => now()->subDays($this->faker->numberBetween(0, 30))->format('Y-m-d'),
        ]);
    }
}
