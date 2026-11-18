<?php

namespace Tests\Feature;

use App\Models\Coach;
use App\Models\CoachPayroll;
use App\Models\CoachRate;
use App\Models\Enrollment;
use App\Models\Event;
use App\Models\EventParticipant;
use App\Models\Guardian;
use App\Models\Item;
use App\Models\Program;
use App\Models\StockMovement;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Memastikan halaman resource Fase A–C benar-benar bisa dibuka admin
 * (tabel & form ter-render) dan ditolak untuk pelatih.
 */
class FilamentPhaseCResourcesTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_list_and_create_pages(): void
    {
        $this->actingAs(User::factory()->admin()->create());

        foreach ([
            '/admin/guardians',
            '/admin/guardians/create',
            '/admin/items',
            '/admin/items/create',
            '/admin/stock-movements',
            '/admin/stock-movements/create',
            '/admin/enrollments',
            '/admin/enrollments/create',
            '/admin/event-participants',
            '/admin/event-participants/create',
            '/admin/coach-rates',
            '/admin/coach-rates/create',
            '/admin/coach-payrolls',
            '/admin/coach-payrolls/create',
        ] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_admin_can_open_edit_pages(): void
    {
        $this->actingAs(User::factory()->admin()->create());

        $guardian = Guardian::create(['name' => 'Wali Test']);
        $item = Item::create([
            'code' => 'T-1',
            'name' => 'Seragam Test',
            'category' => 'seragam',
            'unit' => 'pcs',
            'price' => 100000,
            'stock' => 5,
            'min_stock' => 1,
            'is_active' => true,
        ]);
        StockMovement::create(['item_id' => $item->id, 'type' => 'in', 'quantity' => 2]);

        $student = Student::factory()->create();
        $program = Program::factory()->create();

        $enrollment = Enrollment::create([
            'student_id' => $student->id,
            'program_id' => $program->id,
            'status' => 'active',
            'started_at' => now()->toDateString(),
        ]);

        $event = Event::factory()->create();
        $participant = EventParticipant::create([
            'event_id' => $event->id,
            'student_id' => $student->id,
            'status' => 'registered',
        ]);

        $coach = Coach::factory()->create();
        $rate = CoachRate::create([
            'coach_id' => $coach->id,
            'rate_type' => 'per_session',
            'amount' => 50000,
            'effective_from' => now()->toDateString(),
        ]);
        $payroll = CoachPayroll::create([
            'coach_id' => $coach->id,
            'period' => '2026-11',
            'sessions_count' => 1,
            'rate_type' => 'per_session',
            'rate_amount' => 50000,
        ]);

        foreach ([
            "/admin/guardians/{$guardian->id}/edit",
            "/admin/items/{$item->id}/edit",
            "/admin/enrollments/{$enrollment->id}/edit",
            "/admin/event-participants/{$participant->id}/edit",
            "/admin/coach-rates/{$rate->id}/edit",
            "/admin/coach-payrolls/{$payroll->id}/edit",
        ] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_coach_role_is_forbidden_from_new_resources(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'pelatih']));

        foreach ([
            '/admin/guardians',
            '/admin/items',
            '/admin/enrollments',
            '/admin/coach-rates',
            '/admin/coach-payrolls',
        ] as $url) {
            $this->get($url)->assertForbidden();
        }
    }
}
