<?php

namespace Database\Seeders;

use App\Models\Gallery;
use Illuminate\Database\Seeder;

class GallerySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $photos = [
            ['title' => 'Latihan Rutin Anggota', 'category' => 'Latihan Rutin', 'image' => 'images/gallery-1.jpg'],
            ['title' => 'Sesi Pemanasan Bersama', 'category' => 'Latihan Rutin', 'image' => 'images/gallery-2.jpg'],
            ['title' => 'Teknik Dasar Tapak Suci', 'category' => 'Latihan Rutin', 'image' => 'images/gallery-3.jpg'],
            ['title' => 'Kejuaraan Antar Dojo', 'category' => 'Kejuaraan', 'image' => 'images/gallery-4.jpg'],
            ['title' => 'Persiapan Turnamen', 'category' => 'Kejuaraan', 'image' => 'images/gallery-5.jpg'],
            ['title' => 'Ujian Kenaikan Tingkat', 'category' => 'Ujian Tingkat', 'image' => 'images/gallery-6.jpg'],
            ['title' => 'Pembinaan Mental & Disiplin', 'category' => 'Latihan Rutin', 'image' => 'images/gallery-7.jpg'],
            ['title' => 'Bakti Sosial Akademi', 'category' => 'Kegiatan Sosial', 'image' => 'images/gallery-8.jpg'],
            ['title' => 'Penyerahan Penghargaan', 'category' => 'Kegiatan Sosial', 'image' => 'images/gallery-9.jpg'],
        ];

        foreach ($photos as $index => $photo) {
            Gallery::updateOrCreate(
                ['image' => $photo['image']],
                [
                    'title' => $photo['title'],
                    'category' => $photo['category'],
                    'description' => null,
                    'taken_at' => now()->subDays(($index + 1) * 7)->toDateString(),
                    'is_published' => true,
                    'sort_order' => $index + 1,
                ],
            );
        }

        $this->command->info('Galeri kegiatan siap ('.count($photos).' foto).');
    }
}
