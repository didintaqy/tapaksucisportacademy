<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class PolicyCoverageTest extends TestCase
{
    use RefreshDatabase;

    public function test_every_filament_resource_model_has_a_policy(): void
    {
        $resourceFiles = glob(app_path('Filament/Resources/*/*Resource.php'));

        $this->assertNotEmpty($resourceFiles, 'Tidak ada resource Filament yang ditemukan.');

        foreach ($resourceFiles as $file) {
            $resource = 'App\\Filament\\Resources\\'.basename(dirname($file)).'\\'.basename($file, '.php');

            if (! class_exists($resource)) {
                $this->fail("Resource [{$resource}] tidak dapat dimuat.");
            }

            $model = $resource::getModel();

            $this->assertNotNull(
                Gate::getPolicyFor($model),
                "Model [{$model}] dipakai oleh resource [{$resource}] tetapi belum memiliki policy.",
            );
        }
    }
}
