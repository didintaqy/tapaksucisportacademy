<?php

namespace Tests\Feature;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class MidtransGatewayTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('payment.default', 'midtrans');
        config()->set('payment.gateways.midtrans.server_key', 'SB-Mid-server-TESTKEY');
        config()->set('payment.gateways.midtrans.is_production', false);
    }

    public function test_checkout_creates_qris_charge_via_midtrans(): void
    {
        Http::fake([
            'api.sandbox.midtrans.com/*' => Http::response([
                'status_code' => '201',
                'transaction_id' => 'trx-123',
                'order_id' => 'INV-TEST',
                'qr_string' => '00020101021226610014ID.CO.QRIS.WWW',
                'expiry_time' => '2026-10-18 10:00:00',
                'actions' => [
                    [
                        'name' => 'generate-qr-code',
                        'method' => 'GET',
                        'url' => 'https://api.sandbox.midtrans.com/v2/qris/trx-123/qr-code',
                    ],
                ],
            ], 201),
        ]);

        $student = Student::factory()->create();
        $payment = Payment::factory()->create(['student_id' => $student->id]);

        $response = $this->withSession(['payment_portal_student_id' => $student->id])
            ->post(route('payments.portal.checkout', $payment));

        $response->assertRedirect(route('payments.portal.checkout.show', $payment));

        $payment->refresh();
        $this->assertSame(PaymentStatus::Pending, $payment->status);
        $this->assertSame('midtrans', $payment->gateway_name);
        $this->assertSame('trx-123', $payment->gateway_reference);
        $this->assertSame('00020101021226610014ID.CO.QRIS.WWW', data_get($payment->gateway_payload, 'qr_string'));
        $this->assertSame(
            'https://api.sandbox.midtrans.com/v2/qris/trx-123/qr-code',
            data_get($payment->gateway_payload, 'qr_url'),
        );

        Http::assertSent(function ($request) {
            return str_contains($request->url(), '/v2/charge')
                && $request['payment_type'] === 'qris'
                && $request['transaction_details']['order_id'] !== null;
        });
    }

    public function test_callback_with_valid_signature_marks_payment_paid(): void
    {
        $payment = Payment::factory()->create([
            'amount' => 150000,
            'status' => PaymentStatus::Pending,
        ]);

        $orderId = $payment->invoice_number;
        $statusCode = '200';
        $grossAmount = number_format((float) $payment->amount, 2, '.', '');
        $signature = hash('sha512', $orderId.$statusCode.$grossAmount.'SB-Mid-server-TESTKEY');

        $response = $this->postJson(route('payments.callback'), [
            'order_id' => $orderId,
            'status_code' => $statusCode,
            'gross_amount' => $grossAmount,
            'signature_key' => $signature,
            'transaction_status' => 'settlement',
            'fraud_status' => 'accept',
            'transaction_id' => 'trx-999',
            'payment_type' => 'qris',
        ]);

        $response->assertOk();

        $payment->refresh();
        $this->assertTrue($payment->isPaid());
        $this->assertNotNull($payment->paid_at);
    }

    public function test_callback_with_invalid_signature_is_rejected(): void
    {
        $payment = Payment::factory()->create(['status' => PaymentStatus::Pending]);

        $response = $this->postJson(route('payments.callback'), [
            'order_id' => $payment->invoice_number,
            'status_code' => '200',
            'gross_amount' => '150000.00',
            'signature_key' => 'invalid-signature',
            'transaction_status' => 'settlement',
        ]);

        $response->assertStatus(401);

        $payment->refresh();
        $this->assertFalse($payment->isPaid());
    }

    public function test_callback_with_unpaid_status_updates_payment_status(): void
    {
        $payment = Payment::factory()->create([
            'amount' => 150000,
            'status' => PaymentStatus::Pending,
        ]);

        $orderId = $payment->invoice_number;
        $statusCode = '201';
        $grossAmount = number_format((float) $payment->amount, 2, '.', '');
        $signature = hash('sha512', $orderId.$statusCode.$grossAmount.'SB-Mid-server-TESTKEY');

        $this->postJson(route('payments.callback'), [
            'order_id' => $orderId,
            'status_code' => $statusCode,
            'gross_amount' => $grossAmount,
            'signature_key' => $signature,
            'transaction_status' => 'expire',
        ])->assertOk();

        $payment->refresh();
        $this->assertSame(PaymentStatus::Expired, $payment->status);
    }

    public function test_portal_receipt_available_for_paid_payment(): void
    {
        $student = Student::factory()->create();
        $payment = Payment::factory()->paid()->create(['student_id' => $student->id]);

        $response = $this->withSession(['payment_portal_student_id' => $student->id])
            ->get(route('payments.portal.receipt', $payment));

        $response->assertOk();
        $response->assertSee($payment->invoice_number);
    }

    public function test_portal_receipt_blocked_for_unpaid_payment(): void
    {
        $student = Student::factory()->create();
        $payment = Payment::factory()->create(['student_id' => $student->id]);

        $this->withSession(['payment_portal_student_id' => $student->id])
            ->get(route('payments.portal.receipt', $payment))
            ->assertForbidden();
    }
}
