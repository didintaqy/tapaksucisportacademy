<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use App\Services\SiteSettings;
use Illuminate\Database\Seeder;

class SiteSettingSeeder extends Seeder
{
    /**
     * Seed pengaturan awal tanpa menimpa perubahan yang sudah dibuat admin.
     */
    public function run(): void
    {
        SiteSetting::query()->firstOrCreate(
            ['id' => 1],
            ['data' => app(SiteSettings::class)->defaults()],
        );
    }
}
