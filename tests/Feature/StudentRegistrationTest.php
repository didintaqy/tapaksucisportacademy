<?php

namespace Tests\Feature;

use App\Enums\RegistrationStatus;
use App\Enums\UserRole;
use App\Models\StudentRegistration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentRegistrationTest extends TestCase
{
    use RefreshDatabase;

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'full_name' => 'Muhammad Rizky',
            'nickname' => 'Rizky',
            'gender' => 'L',
            'birth_place' => 'Batam',
            'birth_date' => '2015-06-12',
            'education' => 'SD',
            'parent_name' => 'Andi Pratama',
            'phone' => '081234567801',
            'email' => 'andi@example.com',
            'address' => 'Perumahan Cendana Blok F No. 4, Batam',
            'branch' => 'Cabang Pusat',
            'notes' => 'Belum pernah ikut silat.',
        ], $overrides);
    }

    public function test_registration_page_loads(): void
    {
        $this->get(route('registration'))
            ->assertOk()
            ->assertSee('Formulir Pendaftaran')
            ->assertSee('Kirim Pendaftaran');
    }

    public function test_public_registration_is_stored_as_new(): void
    {
        $response = $this->post(route('registration.store'), $this->payload());

        $response->assertRedirect(route('registration'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('student_registrations', [
            'full_name' => 'Muhammad Rizky',
            'phone' => '081234567801',
            'status' => RegistrationStatus::New->value,
        ]);
    }

    public function test_public_registration_validates_required_fields(): void
    {
        $this->post(route('registration.store'), [
            'full_name' => '',
            'gender' => 'X',
            'birth_date' => now()->addDay()->format('Y-m-d'),
            'parent_name' => '',
            'phone' => '',
            'address' => '',
        ])->assertSessionHasErrors([
            'full_name',
            'gender',
            'birth_date',
            'parent_name',
            'phone',
            'address',
        ]);
    }

    public function test_honeypot_blocks_spam_registrations(): void
    {
        $this->post(route('registration.store'), $this->payload([
            'website' => 'https://spam.example',
        ]))->assertSessionHasErrors('website');

        $this->assertDatabaseCount('student_registrations', 0);
    }

    public function test_admin_can_mark_registration_as_accepted(): void
    {
        $registration = StudentRegistration::factory()->create();
        $admin = User::factory()->admin()->create();

        $registration->markAs(RegistrationStatus::Accepted, $admin);

        $registration->refresh();
        $this->assertSame(RegistrationStatus::Accepted, $registration->status);
        $this->assertSame($admin->id, $registration->reviewed_by);
        $this->assertNotNull($registration->reviewed_at);
    }

    public function test_registration_permissions(): void
    {
        $admin = User::factory()->admin()->create();
        $operator = User::factory()->create(['role' => UserRole::Operator]);
        $coach = User::factory()->create(['role' => UserRole::Pelatih]);
        $registration = StudentRegistration::factory()->create();

        $this->assertTrue($admin->can('viewAny', StudentRegistration::class));
        $this->assertTrue($operator->can('update', $registration));
        $this->assertFalse($coach->can('viewAny', StudentRegistration::class));
        $this->assertFalse($operator->can('delete', $registration));
        $this->assertTrue($admin->can('delete', $registration));
    }
}
