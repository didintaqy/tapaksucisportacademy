<?php

namespace Database\Seeders;

use App\Models\Program;
use App\Models\TrainingSchedule;
use Illuminate\Database\Seeder;

class TrainingScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $schedules = [
            [
                'class_name' => 'Kelas Anak Pemula',
                'branch' => 'Cabang Pusat',
                'day_of_week' => 1,
                'start_time' => '16:00',
                'end_time' => '17:30',
                'location' => 'Aula Latihan Cabang Pusat',
                'coach_name' => 'Ustadz Ahmad',
                'sort_order' => 1,
            ],
            [
                'class_name' => 'Kelas Remaja',
                'branch' => 'Cabang Pusat',
                'day_of_week' => 3,
                'start_time' => '16:00',
                'end_time' => '17:30',
                'location' => 'Aula Latihan Cabang Pusat',
                'coach_name' => 'Ustadz Ahmad',
                'sort_order' => 2,
            ],
            [
                'class_name' => 'Kelas Dewasa',
                'branch' => 'Cabang Pusat',
                'day_of_week' => 5,
                'start_time' => '19:30',
                'end_time' => '21:00',
                'location' => 'Aula Latihan Cabang Pusat',
                'coach_name' => 'Ustadz Hasan',
                'sort_order' => 3,
            ],
            [
                'class_name' => 'Kelas Anak Pemula',
                'branch' => 'Cabang Batam Kota',
                'day_of_week' => 2,
                'start_time' => '16:00',
                'end_time' => '17:30',
                'location' => 'Gedung Serbaguna Batam Kota',
                'coach_name' => 'Ustadzah Nur',
                'sort_order' => 4,
            ],
            [
                'class_name' => 'Kelas Remaja & Dewasa',
                'branch' => 'Cabang Batam Kota',
                'day_of_week' => 6,
                'start_time' => '08:00',
                'end_time' => '10:00',
                'location' => 'Gedung Serbaguna Batam Kota',
                'coach_name' => 'Ustadzah Nur',
                'sort_order' => 5,
            ],
        ];

        $programIds = Program::query()->pluck('id', 'slug');
        $programMap = [
            'Kelas Anak Pemula' => 'kelas-dasar-tapak-suci',
            'Kelas Remaja' => 'kelas-remaja',
            'Kelas Dewasa' => 'kelas-dewasa',
            'Kelas Remaja & Dewasa' => 'kelas-dewasa',
        ];

        foreach ($schedules as $schedule) {
            $slug = $programMap[$schedule['class_name']] ?? null;
            $programId = $slug ? ($programIds[$slug] ?? null) : null;

            TrainingSchedule::updateOrCreate(
                [
                    'branch' => $schedule['branch'],
                    'class_name' => $schedule['class_name'],
                    'day_of_week' => $schedule['day_of_week'],
                ],
                [...$schedule, 'program_id' => $programId, 'is_active' => true],
            );
        }
    }
}
