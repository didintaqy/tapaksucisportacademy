<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Latihan', 'slug' => 'latihan', 'description' => 'Artikel tentang latihan pencak silat'],
            ['name' => 'Tips & Trik', 'slug' => 'tips-trik', 'description' => 'Tips meningkatkan keterampilan'],
            ['name' => 'Kejuaraan', 'slug' => 'kejuaraan', 'description' => 'Berita dan informasi kejuaraan'],
            ['name' => 'Event', 'slug' => 'event', 'description' => 'Kegiatan dan acara akademi'],
            ['name' => 'Sejarah', 'slug' => 'sejarah', 'description' => 'Sejarah dan filsafat Tapak Suci'],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(['name' => $cat['name']], $cat);
        }
    }
}
