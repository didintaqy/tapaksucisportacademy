<?php

namespace Tests\Feature;

use App\Models\TrainingSchedule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FilamentTrainingScheduleResourceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->admin()->create());
    }

    public function test_training_schedule_resource_pages_load(): void
    {
        $schedule = TrainingSchedule::factory()->create();

        $this->get('/admin/training-schedules')->assertOk();
        $this->get('/admin/training-schedules/create')->assertOk();
        $this->get("/admin/training-schedules/{$schedule->id}/edit")->assertOk();
    }
}
