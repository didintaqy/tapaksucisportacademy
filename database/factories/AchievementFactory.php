<?php

namespace Database\Factories;

use App\Enums\CompetitionLevel;
use App\Enums\MedalType;
use App\Models\Achievement;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Achievement>
 */
class AchievementFactory extends Factory
{
    protected $model = Achievement::class;

    public function definition(): array
    {
        return [
            'student_id' => Student::factory(),
            'competition_name' => $this->faker->randomElement([
                'Kejuaraan Daerah 5 Tapak Suci Batam',
                'King of Silat Championship Kepri',
                'O2SN Pencak Silat Tingkat Kota Batam',
                'Kejuaraan Pencak Silat Antar Pelajar Se-Kepri',
                'Batam Open Pencak Silat Championship',
            ]),
            'level' => $this->faker->randomElement(CompetitionLevel::cases()),
            'event_date' => $this->faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'location' => 'Batam, Kepulauan Riau',
            'organizer' => 'IPSI / Pimda Tapak Suci Batam',
            'category' => $this->faker->randomElement(['Tanding Usia Dini', 'Tanding Pra Remaja', 'Tanding Remaja', 'Seni Tunggal Baku']),
            'weight_class' => $this->faker->randomElement(['Kelas A', 'Kelas B', 'Kelas C', 'Kelas D', 'Tunggal']),
            'medal' => $this->faker->randomElement([MedalType::Emas, MedalType::Perak, MedalType::Perunggu, MedalType::JuaraUmum]),
            'certificate_file' => null,
            'notes' => $this->faker->optional()->sentence(),
            'is_published' => true,
        ];
    }
}
