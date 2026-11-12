<?php

namespace Database\Seeders;

use App\Enums\CompetitionLevel;
use App\Enums\MedalType;
use App\Models\Achievement;
use App\Models\Student;
use Illuminate\Database\Seeder;

class AchievementSeeder extends Seeder
{
    public function run(): void
    {
        $students = Student::orderBy('id')->take(8)->get();

        $records = [
            [
                'competition_name' => 'Kejuaraan Daerah 5 Tapak Suci',
                'level' => CompetitionLevel::Provinsi->value,
                'event_date' => '2026-08-18',
                'location' => 'Sport Hall Temenggung Abdul Jamal, Batam',
                'organizer' => 'Pimda Tapak Suci Kota Batam',
                'category' => 'Kontingen Usia Dini',
                'weight_class' => 'Juara Umum 1 (31 Emas, 23 Perak, 11 Perunggu)',
                'medal' => MedalType::JuaraUmum->value,
                'notes' => 'Juara Umum 1 Kategori Usia Dini dengan perolehan 31 Emas, 23 Perak, dan 11 Perunggu.',
                'student_index' => null,
            ],
            [
                'competition_name' => 'King of Silat Championship',
                'level' => CompetitionLevel::Regional->value,
                'event_date' => '2026-06-14',
                'location' => 'Mega Mall Batam Centre',
                'organizer' => 'IPSI Kepulauan Riau',
                'category' => 'Kontingen Akademi',
                'weight_class' => 'Juara Umum 1',
                'medal' => MedalType::JuaraUmum->value,
                'notes' => 'Meraih gelar Juara Umum 1 tingkat regional Kepulauan Riau.',
                'student_index' => null,
            ],
            [
                'competition_name' => 'Kejuaraan Daerah 5 Tapak Suci',
                'level' => CompetitionLevel::Provinsi->value,
                'event_date' => '2026-08-18',
                'location' => 'Batam',
                'organizer' => 'Pimda Tapak Suci Kota Batam',
                'category' => 'Tanding Pra Remaja Putra',
                'weight_class' => 'Kelas D',
                'medal' => MedalType::Emas->value,
                'notes' => 'Penampilan dominan di babak final.',
                'student_index' => 0,
            ],
            [
                'competition_name' => 'O2SN Pencak Silat Tingkat Kota Batam',
                'level' => CompetitionLevel::Kota->value,
                'event_date' => '2026-05-20',
                'location' => 'Batam',
                'organizer' => 'Dinas Pendidikan Kota Batam',
                'category' => 'Seni Tunggal Baku',
                'weight_class' => 'Tunggal SMP',
                'medal' => MedalType::Emas->value,
                'notes' => 'Mewakili sekolah dan akademi di tingkat kota.',
                'student_index' => 1,
            ],
            [
                'competition_name' => 'King of Silat Championship',
                'level' => CompetitionLevel::Regional->value,
                'event_date' => '2026-06-14',
                'location' => 'Batam',
                'organizer' => 'IPSI Kepulauan Riau',
                'category' => 'Tanding Usia Dini',
                'weight_class' => 'Kelas B',
                'medal' => MedalType::Perak->value,
                'notes' => 'Finalis kategori tanding usia dini.',
                'student_index' => 2,
            ],
            [
                'competition_name' => 'Kejuaraan Pencak Silat Antar Pelajar Kepri',
                'level' => CompetitionLevel::Provinsi->value,
                'event_date' => '2026-03-10',
                'location' => 'Tanjungpinang',
                'organizer' => 'Dispora Kepulauan Riau',
                'category' => 'Tanding Remaja',
                'weight_class' => 'Kelas C',
                'medal' => MedalType::Perunggu->value,
                'notes' => 'Semifinalis kejuaraan pelajar tingkat provinsi.',
                'student_index' => 3,
            ],
        ];

        foreach ($records as $row) {
            $studentId = null;
            if ($row['student_index'] !== null && isset($students[$row['student_index']])) {
                $studentId = $students[$row['student_index']]->id;
            }

            Achievement::firstOrCreate(
                [
                    'competition_name' => $row['competition_name'],
                    'event_date' => $row['event_date'],
                    'category' => $row['category'],
                    'weight_class' => $row['weight_class'],
                    'student_id' => $studentId,
                ],
                [
                    'level' => $row['level'],
                    'location' => $row['location'],
                    'organizer' => $row['organizer'],
                    'medal' => $row['medal'],
                    'notes' => $row['notes'],
                    'is_published' => true,
                ],
            );
        }
    }
}
