<?php

namespace Tests\Feature;

use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CalendarTest extends TestCase
{
    use RefreshDatabase;

    public function test_calendar_page_loads(): void
    {
        $this->get(route('calendar'))
            ->assertOk()
            ->assertSee('Kalender Kegiatan')
            ->assertSee('Jadwal latihan, kejuaraan, UKT, dan pengumuman')
            ->assertSee('Min')
            ->assertSee('Sen')
            ->assertSee('Sel')
            ->assertSee('Rab')
            ->assertSee('Kam')
            ->assertSee('Jum')
            ->assertSee('Sab');
    }

    public function test_calendar_shows_events_for_current_month(): void
    {
        $event = Event::factory()->create([
            'title' => 'Test Event',
            'starts_at' => now()->startOfMonth()->addDays(5)->setTime(10, 0),
            'type' => 'latihan',
            'is_public' => true,
        ]);

        $this->get(route('calendar'))
            ->assertOk()
            ->assertSee('Test Event');
    }

    public function test_calendar_filters_by_month(): void
    {
        $nextMonth = now()->addMonth();
        $event = Event::factory()->create([
            'title' => 'Next Month Event',
            'starts_at' => $nextMonth->startOfMonth()->addDays(3)->setTime(10, 0),
            'type' => 'kejuaraan',
            'is_public' => true,
        ]);

        $this->get(route('calendar', ['month' => $nextMonth->month, 'year' => $nextMonth->year]))
            ->assertOk()
            ->assertSee('Next Month Event');
    }

    public function test_calendar_hides_non_public_events(): void
    {
        Event::factory()->create([
            'title' => 'Private Event',
            'starts_at' => now()->startOfMonth()->addDays(2)->setTime(10, 0),
            'type' => 'latihan',
            'is_public' => false,
        ]);

        $this->get(route('calendar'))
            ->assertOk()
            ->assertDontSee('Private Event');
    }

    public function test_calendar_past_events_not_shown_in_future_month(): void
    {
        Event::factory()->create([
            'title' => 'Past Event',
            'starts_at' => now()->subMonth()->startOfMonth()->addDays(2)->setTime(10, 0),
            'type' => 'latihan',
            'is_public' => true,
        ]);

        $this->get(route('calendar', ['month' => now()->month, 'year' => now()->year]))
            ->assertOk()
            ->assertDontSee('Past Event');
    }
}
