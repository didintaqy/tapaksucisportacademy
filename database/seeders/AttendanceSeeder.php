<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\Student;
use App\Models\TrainingSchedule;
use App\Models\User;
use Faker\Generator as Faker;
use Illuminate\Database\Seeder;

class AttendanceSeeder extends Seeder
{
    public function run(Faker $faker): void
    {
        $coaches = User::where('role', 'pelatih')->get();
        $schedules = TrainingSchedule::all();
        $students = Student::all();

        if ($schedules->isEmpty() || $students->isEmpty()) {
            $this->command->warn('TrainingSchedule atau Student kosong, seeder attendance dilewati.');

            return;
        }

        $defaultCoach = $coaches->first() ?? User::where('role', 'admin')->first();

        $count = 0;
        foreach ($schedules as $schedule) {
            // Ambil siswa yang cocok dengan cabang jadwal ini
            $matchingStudents = $students->where('branch', $schedule->branch);
            if ($matchingStudents->isEmpty()) {
                continue;
            }

            // Generate absensi untuk 8 minggu terakhir
            for ($week = 0; $week < 8; $week++) {
                $sessionDate = $schedule->getNextSessionDate(now()->subWeeks($week));
                if (! $sessionDate || $sessionDate->isFuture()) {
                    continue;
                }

                foreach ($matchingStudents->shuffle()->take(rand(5, min(15, $matchingStudents->count()))) as $student) {
                    $status = $faker->randomElement(['hadir', 'hadir', 'hadir', 'hadir', 'izin', 'sakit', 'alpa']); // weight hadir higher

                    Attendance::updateOrCreate(
                        [
                            'student_id' => $student->id,
                            'training_schedule_id' => $schedule->id,
                            'date' => $sessionDate->format('Y-m-d'),
                        ],
                        [
                            'status' => $status,
                            'notes' => in_array($status, ['izin', 'sakit']) ? $faker->sentence() : null,
                            'recorded_by' => $defaultCoach?->id,
                        ]
                    );
                    $count++;
                }
            }
        }

        $this->command->info("Attendance seeder selesai: {$count} record absensi dibuat.");
    }
}
