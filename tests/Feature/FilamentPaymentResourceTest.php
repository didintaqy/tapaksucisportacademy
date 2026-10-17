<?php

namespace Tests\Feature;

use App\Models\Payment;
use App\Models\PaymentType;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FilamentPaymentResourceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create());
    }

    public function test_payment_resource_pages_load(): void
    {
        $payment = Payment::factory()->create();

        $this->get('/admin/payments')->assertOk();
        $this->get('/admin/payments/create')->assertOk();
        $this->get("/admin/payments/{$payment->id}")->assertOk();
        $this->get("/admin/payments/{$payment->id}/edit")->assertOk();
    }

    public function test_payment_type_resource_pages_load(): void
    {
        $type = PaymentType::factory()->create();

        $this->get('/admin/payment-types')->assertOk();
        $this->get('/admin/payment-types/create')->assertOk();
        $this->get("/admin/payment-types/{$type->id}/edit")->assertOk();
    }

    public function test_dashboard_loads_with_payment_overview_widget(): void
    {
        Payment::factory()->create();

        $this->get('/admin')->assertOk();
    }

    public function test_student_edit_page_loads_relations(): void
    {
        $student = Student::factory()->create();
        Payment::factory()->create(['student_id' => $student->id]);

        $this->get("/admin/students/{$student->id}/edit")->assertOk();
    }
}
