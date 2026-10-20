<?php

namespace Tests\Feature;

use App\Models\StudentRegistration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FilamentStudentRegistrationResourceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->admin()->create());
    }

    public function test_student_registration_resource_pages_load(): void
    {
        $registration = StudentRegistration::factory()->create();

        $this->get('/admin/student-registrations')->assertOk();
        $this->get('/admin/student-registrations/create')->assertOk();
        $this->get("/admin/student-registrations/{$registration->id}/edit")->assertOk();
    }
}
