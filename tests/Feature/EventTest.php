<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\User;
use Database\Seeders\EventSeeder;
use Database\Seeders\UsersTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(UsersTableSeeder::class);
        $this->seed(EventSeeder::class);
    }

    public function test_admin_can_view_event_list(): void
    {
        $admin = User::where('role', 'admin')->first();
        $this->actingAs($admin);

        $this->get(route('filament.admin.resources.events.index'))
            ->assertOk()
            ->assertSee('Kalender Kegiatan');
    }

    public function test_operator_can_view_event_list(): void
    {
        $operator = User::where('role', 'operator')->first();
        $this->actingAs($operator);

        $this->get(route('filament.admin.resources.events.index'))
            ->assertOk();
    }

    public function test_pelatih_cannot_view_event_list(): void
    {
        $pelatih = User::where('role', 'pelatih')->first();
        $this->actingAs($pelatih);

        $this->get(route('filament.admin.resources.events.index'))
            ->assertForbidden();
    }

    public function test_event_creation(): void
    {
        $admin = User::where('role', 'admin')->first();

        $event = Event::create([
            'title' => 'Test Event',
            'description' => 'Test description',
            'starts_at' => now()->addDay(),
            'ends_at' => now()->addDay()->addHours(2),
            'type' => 'latihan',
            'is_public' => true,
            'created_by' => $admin->id,
        ]);

        $this->assertDatabaseHas('events', [
            'id' => $event->id,
            'title' => 'Test Event',
            'type' => 'latihan',
        ]);
    }

    public function test_event_type_color_accessor(): void
    {
        $event = Event::factory()->make(['type' => 'latihan']);
        $this->assertEquals('primary', $event->type_color);

        $event->type = 'kejuaraan';
        $this->assertEquals('danger', $event->type_color);

        $event->type = 'ukt';
        $this->assertEquals('warning', $event->type_color);

        $event->type = 'pengumuman';
        $this->assertEquals('info', $event->type_color);

        $event->type = 'libur';
        $this->assertEquals('secondary', $event->type_color);
    }

    public function test_event_type_label_accessor(): void
    {
        $event = Event::factory()->make(['type' => 'latihan']);
        $this->assertEquals('Latihan', $event->type_label);

        $event->type = 'kejuaraan';
        $this->assertEquals('Kejuaraan', $event->type_label);

        $event->type = 'ukt';
        $this->assertEquals('UKT', $event->type_label);
    }

    public function test_event_scopes(): void
    {
        $now = now();
        $past = Event::factory()->create(['starts_at' => $now->subDay(), 'is_public' => true]);
        $future = Event::factory()->create(['starts_at' => $now->addDay(), 'is_public' => true]);
        $private = Event::factory()->create(['starts_at' => $now->addDay(), 'is_public' => false]);

        $this->assertTrue(Event::public()->where('id', $past->id)->exists());
        $this->assertTrue(Event::public()->where('id', $future->id)->exists());
        $this->assertFalse(Event::public()->where('id', $private->id)->exists());

        $this->assertTrue(Event::upcoming()->where('id', $future->id)->exists());
        $this->assertFalse(Event::upcoming()->where('id', $past->id)->exists());

        $this->assertTrue(Event::past()->where('id', $past->id)->exists());
        $this->assertFalse(Event::past()->where('id', $future->id)->exists());
    }
}
