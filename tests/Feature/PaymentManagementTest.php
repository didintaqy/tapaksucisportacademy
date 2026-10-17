<?php

namespace Tests\Feature;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_invoice_number_is_auto_generated(): void
    {
        $payment = Payment::factory()->create();

        $this->assertNotEmpty($payment->invoice_number);
        $this->assertStringStartsWith('INV-', $payment->invoice_number);
    }

    public function test_mark_as_paid_updates_status_and_timestamp(): void
    {
        $payment = Payment::factory()->create();

        $payment->markAsPaid(PaymentMethod::Cash);
        $payment->refresh();

        $this->assertTrue($payment->isPaid());
        $this->assertSame(PaymentMethod::Cash, $payment->payment_method);
        $this->assertNotNull($payment->paid_at);
    }

    public function test_portal_search_finds_student_by_id_and_redirects(): void
    {
        $student = Student::factory()->create();

        $response = $this->post(route('payments.portal.search'), [
            'student_id' => (string) $student->id,
        ]);

        $response->assertRedirect(route('payments.portal.bills'));
        $this->assertSame($student->id, session('payment_portal_student_id'));
    }

    public function test_portal_search_by_nik(): void
    {
        $student = Student::factory()->create(['nik' => '2171100903170001']);

        $response = $this->post(route('payments.portal.search'), [
            'student_id' => '2171100903170001',
        ]);

        $response->assertRedirect(route('payments.portal.bills'));
    }

    public function test_portal_bills_page_lists_student_payment(): void
    {
        $student = Student::factory()->create();
        Payment::factory()->create([
            'student_id' => $student->id,
            'amount' => 150000,
        ]);

        $response = $this->withSession(['payment_portal_student_id' => $student->id])
            ->get(route('payments.portal.bills'));

        $response->assertOk();
        $response->assertSee($student->full_name);
    }

    public function test_checkout_creates_charge_and_redirects_to_checkout(): void
    {
        $student = Student::factory()->create();
        $payment = Payment::factory()->create(['student_id' => $student->id]);

        $response = $this->withSession(['payment_portal_student_id' => $student->id])
            ->post(route('payments.portal.checkout', $payment));

        $response->assertRedirect(route('payments.portal.checkout.show', $payment));

        $payment->refresh();
        $this->assertSame(PaymentStatus::Pending, $payment->status);
        $this->assertSame('manual', $payment->gateway_name);
        $this->assertNotEmpty(data_get($payment->gateway_payload, 'qr_string'));
    }

    public function test_checkout_page_renders_qris(): void
    {
        $student = Student::factory()->create();
        $payment = Payment::factory()->create(['student_id' => $student->id]);

        $this->withSession(['payment_portal_student_id' => $student->id])
            ->post(route('payments.portal.checkout', $payment));

        $response = $this->withSession(['payment_portal_student_id' => $student->id])
            ->get(route('payments.portal.checkout.show', $payment));

        $response->assertOk();
        $response->assertSee($payment->invoice_number);
        $response->assertSee($payment->formattedAmount());
    }

    public function test_cannot_checkout_another_students_payment(): void
    {
        $owner = Student::factory()->create();
        $other = Student::factory()->create();
        $payment = Payment::factory()->create(['student_id' => $owner->id]);

        $response = $this->withSession(['payment_portal_student_id' => $other->id])
            ->post(route('payments.portal.checkout', $payment));

        $response->assertNotFound();
    }

    public function test_receipt_is_accessible_for_authenticated_user(): void
    {
        $user = User::factory()->create();
        $payment = Payment::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('payments.receipt', $payment));

        $response->assertOk();
        $response->assertSee($payment->invoice_number);
    }
}
