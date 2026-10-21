<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        $tags = [
            ['name' => 'pencak silat', 'slug' => 'pencak-silat'],
            ['name' => 'tapak suci', 'slug' => 'tapak-suci'],
            ['name' => 'latihan', 'slug' => 'latihan'],
            ['name' => 'teknik', 'slug' => 'teknik'],
            ['name' => 'pemula', 'slug' => 'pemula'],
            ['name' => 'lanjutan', 'slug' => 'lanjutan'],
            ['name' => 'kompetisi', 'slug' => 'kompetisi'],
            ['name' => 'sabuk', 'slug' => 'sabuk'],
            ['name' => 'filosofi', 'slug' => 'filosofi'],
            ['name' => 'kesehatan', 'slug' => 'kesehatan'],
        ];

        foreach ($tags as $tag) {
            Tag::firstOrCreate(['name' => $tag['name']], $tag);
        }
    }
}
