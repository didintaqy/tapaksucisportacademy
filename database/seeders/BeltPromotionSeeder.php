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
        $results = [
            PromotionResult::Lulus,
            PromotionResult::Lulus,
            PromotionResult::Lulus,
            PromotionResult::TidakLulus,
            PromotionResult::Ditunda,
        ];
        $notesOptions = [
            'Lulus dengan baik.',
            'Perlu perbaikan teknik.',
            'Ditunda hingga bulan depan.',
            'Tidak lulus, coba lagi.',
            'Prestasi gemilang.',
        ];
        $examinerNotesOptions = [
            'Teknik bagus.',
            'Perlu latihan lebih.',
            'Sikap baik.',
            'Kurang fokus.',
        ];

        $count = 0;

        foreach ($students as $student) {
            $currentBeltIndex = array_search($student->belt_level, $beltOrder);
            if ($currentBeltIndex === false || $currentBeltIndex >= count($beltOrder) - 1) {
                continue; // Already at max belt or belt not in order
            }

            // 70% chance
            if (random_int(1, 100) <= 70) {
                $targetBelt = $beltOrder[$currentBeltIndex + 1];
                $result = $results[array_rand($results)];

                // random date between -12 months and now
                $now = time();
                $twelveMonthsAgo = strtotime('-12 months', $now);
                $examTimestamp = random_int($twelveMonthsAgo, $now);
                $examDate = date('Y-m-d', $examTimestamp);

                $promotion = BeltPromotion::create([
                    'student_id' => $student->id,
                    'examined_by' => $coaches->random()->id,
                    'exam_date' => $examDate,
                    'current_belt' => $student->belt_level,
                    'target_belt' => $targetBelt,
                    'result' => $result,
                    'notes' => (random_int(0, 1) ? $notesOptions[array_rand($notesOptions)] : null),
                    'examiner_notes' => (random_int(0, 1) ? $examinerNotesOptions[array_rand($examinerNotesOptions)] : null),
                    'certificate_issued_at' => $result === PromotionResult::Lulus ? $examDate : null,
                    'certificate_number' => $result === PromotionResult::Lulus
                        ? 'UKT-'.date('Y', $examTimestamp).'-'.str_pad($count + 1, 4, '0', STR_PAD_LEFT)
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
