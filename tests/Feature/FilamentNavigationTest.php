<?php

namespace Tests\Feature;

use App\Filament\Resources\StudentPerformances\StudentPerformanceResource;
use App\Filament\Resources\Students\StudentResource;
use App\Filament\Resources\Users\UserResource;
use Tests\TestCase;

class FilamentNavigationTest extends TestCase
{
    public function test_every_resource_has_a_group_and_unique_sort_order(): void
    {
        $allowedGroups = [
            'Master Data',
            'Manajemen Akademi',
            'Keuangan',
            'Manajemen Web',
            'Pengguna & Akses',
        ];

        $resourceFiles = glob(app_path('Filament/Resources/*/*Resource.php'));

        $this->assertNotEmpty($resourceFiles);

        $seen = [];

        foreach ($resourceFiles as $file) {
            $resource = 'App\\Filament\\Resources\\'.basename(dirname($file)).'\\'.basename($file, '.php');
            $group = $resource::getNavigationGroup();
            $sort = $resource::getNavigationSort();

            $this->assertNotNull($group, "Resource [{$resource}] belum punya navigationGroup.");
            $this->assertContains($group, $allowedGroups, "Resource [{$resource}] memakai grup navigasi tak dikenal.");
            $this->assertNotNull($sort, "Resource [{$resource}] belum punya navigationSort.");

            $key = $group.'|'.$sort;

            $this->assertArrayNotHasKey(
                $key,
                $seen,
                "Resource [{$resource}] bertabrakan urutan pada grup [{$group}] sort [{$sort}].",
            );

            $seen[$key] = $resource;
        }
    }

    public function test_indonesian_model_labels_and_record_titles(): void
    {
        $this->assertSame('Siswa', StudentResource::getModelLabel());
        $this->assertSame('Siswa', StudentResource::getPluralModelLabel());
        $this->assertSame('full_name', StudentResource::getRecordTitleAttribute());

        $this->assertSame('Performa Siswa', StudentPerformanceResource::getModelLabel());
        $this->assertSame('Performa Siswa', StudentPerformanceResource::getPluralModelLabel());

        $this->assertSame('Pengguna', UserResource::getModelLabel());
        $this->assertSame('Pengguna', UserResource::getPluralModelLabel());
        $this->assertSame('name', UserResource::getRecordTitleAttribute());
    }
}
