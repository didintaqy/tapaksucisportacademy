<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\TrainingSchedule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrainingScheduleTest extends TestCase
{
    use RefreshDatabase;

    public function test_schedule_page_shows_only_active_schedules(): void
    {
        TrainingSchedule::factory()->create([
            'class_name' => 'Kelas Pemula',
            'branch' => 'Cabang Pusat',
            'day_of_week' => 1,
            'start_time' => '16:00',
            'end_time' => '17:30',
        ]);

        TrainingSchedule::factory()->inactive()->create([
            'class_name' => 'Kelas Nonaktif',
        ]);

        $this->get(route('schedules'))
            ->assertOk()
            ->assertSee('Senin')
            ->assertSee('Kelas Pemula')
            ->assertSee('16:00 - 17:30')
            ->assertDontSee('Kelas Nonaktif');
    }

    public function test_schedule_page_orders_days_from_monday(): void
    {
        TrainingSchedule::factory()->create(['day_of_week' => 3, 'class_name' => 'Kelas Rabu']);
        TrainingSchedule::factory()->create(['day_of_week' => 1, 'class_name' => 'Kelas Senin']);

        $this->get(route('schedules'))
            ->assertOk()
            ->assertSeeInOrder(['Senin', 'Kelas Senin', 'Rabu', 'Kelas Rabu']);
    }

    public function test_schedule_page_renders_when_empty(): void
    {
        $this->get(route('schedules'))
            ->assertOk()
            ->assertSee('Belum ada jadwal latihan');
    }

    public function test_schedule_permissions(): void
    {
        $admin = User::factory()->admin()->create();
        $operator = User::factory()->create(['role' => UserRole::Operator]);
        $coach = User::factory()->create(['role' => UserRole::Pelatih]);
        $schedule = TrainingSchedule::factory()->create();

        $this->assertTrue($admin->can('viewAny', TrainingSchedule::class));
        $this->assertTrue($operator->can('update', $schedule));
        $this->assertFalse($coach->can('viewAny', TrainingSchedule::class));
        $this->assertFalse($operator->can('delete', $schedule));
        $this->assertTrue($admin->can('delete', $schedule));
    }
}
