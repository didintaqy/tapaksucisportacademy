<?php

namespace Database\Seeders;

use App\Enums\TestimonialStatus;
use App\Models\Testimonial;
use Illuminate\Database\Seeder;

class TestimonialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $testimonials = [
            [
                'name' => 'Siti Rahmawati',
                'relation' => 'Orang Tua Siswa',
                'content' => 'Anak saya jadi lebih disiplin dan percaya diri sejak berlatih di Tapak Suci. Pelatihnya sabar dan memperhatikan perkembangan setiap anak.',
                'rating' => 5,
                'status' => TestimonialStatus::Approved,
                'is_featured' => true,
            ],
            [
                'name' => 'Ahmad Fauzan',
                'relation' => 'Siswa',
                'content' => 'Latihan di sini terasa seperti keluarga. Selain teknik silat, kami diajarkan adab dan tanggung jawab.',
                'rating' => 5,
                'status' => TestimonialStatus::Approved,
                'is_featured' => false,
            ],
            [
                'name' => 'Dewi Lestari',
                'relation' => 'Alumni',
                'content' => 'Pengalaman berlatih di Tapak Suci membentuk karakter saya sampai sekarang. Ilmu dan kedisiplinannya sangat berharga.',
                'rating' => 5,
                'status' => TestimonialStatus::Approved,
                'is_featured' => false,
            ],
            [
                'name' => 'Budi Santoso',
                'relation' => 'Orang Tua Siswa',
                'content' => 'Jadwal latihan jelas, komunikasi dengan orang tua juga baik. Anak saya selalu semangat berangkat latihan.',
                'rating' => 4,
                'status' => TestimonialStatus::Approved,
                'is_featured' => false,
            ],
            [
                'name' => 'Rina Marlina',
                'relation' => 'Orang Tua Siswa',
                'content' => 'Saya ingin anak saya lebih percaya diri. Apakah ada kelas untuk pemula usia 8 tahun?',
                'rating' => 5,
                'status' => TestimonialStatus::Pending,
                'is_featured' => false,
            ],
        ];

        foreach ($testimonials as $testimonial) {
            Testimonial::updateOrCreate(
                [
                    'name' => $testimonial['name'],
                    'content' => $testimonial['content'],
                ],
                [
                    ...$testimonial,
                    'published_at' => $testimonial['status'] === TestimonialStatus::Approved ? now() : null,
                ],
            );
        }
    }
}
