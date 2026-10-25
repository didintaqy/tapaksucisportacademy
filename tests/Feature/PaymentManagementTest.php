<?php

namespace Tests\Feature;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Models\PaymentType;
use App\Models\Student;
use App\Models\User;
use App\Services\Payments\MonthlyBillGenerator;
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
        $student = Student::factory()->create(['birth_date' => '2010-05-17']);

        $response = $this->post(route('payments.portal.search'), [
            'student_id' => (string) $student->id,
            'birth_date' => '2010-05-17',
        ]);

        $response->assertRedirect(route('payments.portal.bills'));
        $this->assertSame($student->id, session('payment_portal_student_id'));
    }

    public function test_portal_search_by_nik(): void
    {
        $student = Student::factory()->create([
            'nik' => '2171100903170001',
            'birth_date' => '2017-03-09',
        ]);

        $response = $this->post(route('payments.portal.search'), [
            'student_id' => '2171100903170001',
            'birth_date' => '2017-03-09',
        ]);

        $response->assertRedirect(route('payments.portal.bills'));
    }

    public function test_portal_search_rejects_wrong_birth_date(): void
    {
        $student = Student::factory()->create([
            'nik' => '2171100903170001',
            'birth_date' => '2017-03-09',
        ]);

        $response = $this->from(route('payments.portal'))->post(route('payments.portal.search'), [
            'student_id' => '2171100903170001',
            'birth_date' => '2017-03-10',
        ]);

        $response->assertRedirect(route('payments.portal'));
        $response->assertSessionHas('error');
        $this->assertNull(session('payment_portal_student_id'));
    }

    public function test_portal_search_requires_birth_date(): void
    {
        $student = Student::factory()->create();

        $response = $this->post(route('payments.portal.search'), [
            'student_id' => (string) $student->id,
        ]);

        $response->assertSessionHasErrors('birth_date');
        $this->assertNull(session('payment_portal_student_id'));
    }

    public function test_portal_search_gives_same_message_for_unknown_and_wrong_date(): void
    {
        $student = Student::factory()->create(['birth_date' => '2010-05-17']);

        $wrongDate = $this->from(route('payments.portal'))->post(route('payments.portal.search'), [
            'student_id' => (string) $student->id,
            'birth_date' => '1999-01-01',
        ]);

        $unknown = $this->from(route('payments.portal'))->post(route('payments.portal.search'), [
            'student_id' => '999999999',
            'birth_date' => '1999-01-01',
        ]);

        $this->assertSame(
            $wrongDate->getSession()->get('error'),
            $unknown->getSession()->get('error'),
            'Pesan error tidak boleh membocorkan apakah suatu ID/NIK terdaftar.',
        );
    }

    public function test_portal_search_rejects_student_without_birth_date(): void
    {
        $student = Student::factory()->create(['birth_date' => null]);

        $this->from(route('payments.portal'))->post(route('payments.portal.search'), [
            'student_id' => (string) $student->id,
            'birth_date' => '2010-05-17',
        ])->assertRedirect(route('payments.portal'));

        $this->assertNull(session('payment_portal_student_id'));
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

    public function test_monthly_generator_creates_one_bill_per_student_and_skips_existing(): void
    {
        $type = PaymentType::factory()->monthly()->create(['default_amount' => 150000]);
        Student::factory()->count(3)->create();
        $already = Student::factory()->create();

        Payment::factory()->create([
            'student_id' => $already->id,
            'payment_type_id' => $type->id,
            'period' => now()->format('Y-m'),
        ]);

        $result = app(MonthlyBillGenerator::class)->generate($type, now()->startOfMonth());

        $this->assertSame(3, $result['created']);
        $this->assertSame(1, $result['skipped']);
        $this->assertSame(4, Payment::query()
            ->where('payment_type_id', $type->id)
            ->where('period', now()->format('Y-m'))
            ->count());
        $this->assertSame(PaymentStatus::Unpaid, Payment::query()->where('student_id', '!=', $already->id)->first()->status);
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
