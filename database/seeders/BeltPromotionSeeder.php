<?php

namespace Database\Seeders;

use App\Enums\PromotionResult;
use App\Models\BeltPromotion;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;

class BeltPromotionSeeder extends Seeder
{
    public function run(): void
    {
        $students = Student::all();
        $coaches = User::where('role', 'pelatih')->get();

        if ($students->isEmpty() || $coaches->isEmpty()) {
            $this->command->warn('Student atau Pelatih kosong, seeder belt promotion dilewati.');

            return;
        }

        $beltOrder = ['putih', 'kuning', 'hijau', 'biru', 'merah', 'coklat', 'hitam_1'];
        $count = 0;

        foreach ($students as $student) {
            $currentBeltIndex = array_search($student->belt_level, $beltOrder);
            if ($currentBeltIndex === false || $currentBeltIndex >= count($beltOrder) - 1) {
                continue; // Already at max belt or belt not in order
            }

            // Randomly decide if this student has a promotion record
            if (fake()->boolean(70)) { // 70% chance
                $targetBelt = $beltOrder[$currentBeltIndex + 1];
                $result = fake()->randomElement([
                    PromotionResult::Lulus,
                    PromotionResult::Lulus,
                    PromotionResult::Lulus,
                    PromotionResult::TidakLulus,
                    PromotionResult::Ditunda,
                ]);

                $examDate = fake()->dateTimeBetween('-12 months', 'now');

                $promotion = BeltPromotion::create([
                    'student_id' => $student->id,
                    'examined_by' => $coaches->random()->id,
                    'exam_date' => $examDate->format('Y-m-d'),
                    'current_belt' => $student->belt_level,
                    'target_belt' => $targetBelt,
                    'result' => $result,
                    'notes' => fake()->optional()->paragraph(),
                    'examiner_notes' => fake()->optional()->sentence(),
                    'certificate_issued_at' => $result === PromotionResult::Lulus ? $examDate->format('Y-m-d') : null,
                    'certificate_number' => $result === PromotionResult::Lulus
                        ? 'UKT-'.$examDate->format('Y').'-'.str_pad($count + 1, 4, '0', STR_PAD_LEFT)
                        : null,
                ]);

                // If passed, update student's belt level
                if ($result === PromotionResult::Lulus) {
                    $student->update(['belt_level' => $targetBelt]);
                }

                $count++;
            }
        }

        $this->command->info("BeltPromotion seeder selesai: {$count} record UKT dibuat.");
    }
}
