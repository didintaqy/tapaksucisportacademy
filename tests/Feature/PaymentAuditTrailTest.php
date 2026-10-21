<?php

namespace Tests\Feature;

use App\Enums\PaymentStatus;
use App\Enums\UserRole;
use App\Models\Payment;
use App\Models\PaymentStatusHistory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentAuditTrailTest extends TestCase
{
    use RefreshDatabase;

    public function test_status_change_creates_history_record(): void
    {
        $admin = User::factory()->admin()->create();
        $payment = Payment::factory()->create(['status' => PaymentStatus::Unpaid]);

        $this->actingAs($admin);

        $payment->markAsPaid();

        $history = PaymentStatusHistory::where('payment_id', $payment->id)->first();

        $this->assertNotNull($history);
        $this->assertSame(PaymentStatus::Unpaid, $history->from_status);
        $this->assertSame(PaymentStatus::Paid, $history->to_status);
        $this->assertSame($admin->id, $history->changed_by);
        $this->assertSame('Pembayaran ditandai lunas', $history->reason);
    }

    public function test_no_history_when_status_unchanged(): void
    {
        $payment = Payment::factory()->create(['status' => PaymentStatus::Unpaid]);

        $payment->update(['notes' => 'Hanya mengubah catatan']);

        $this->assertSame(0, PaymentStatusHistory::where('payment_id', $payment->id)->count());
    }

    public function test_change_status_records_custom_reason_and_metadata(): void
    {
        $payment = Payment::factory()->create(['status' => PaymentStatus::Unpaid]);

        $payment->changeStatus(
            PaymentStatus::Cancelled,
            'Tagihan ganda',
            ['source' => 'manual', 'duplicate_of' => 'INV-001'],
        );

        $history = PaymentStatusHistory::where('payment_id', $payment->id)->firstOrFail();

        $this->assertSame(PaymentStatus::Cancelled, $history->to_status);
        $this->assertSame('Tagihan ganda', $history->reason);
        $this->assertSame(['source' => 'manual', 'duplicate_of' => 'INV-001'], $history->metadata);
    }

    public function test_history_is_ordered_newest_first(): void
    {
        $payment = Payment::factory()->create(['status' => PaymentStatus::Unpaid]);

        $payment->changeStatus(PaymentStatus::Pending, 'Menunggu transfer');
        $this->travel(1)->minutes();
        $payment->changeStatus(PaymentStatus::Paid, 'Transfer diterima');

        $histories = $payment->statusHistories()->get();

        $this->assertCount(2, $histories);
        $this->assertSame(PaymentStatus::Paid, $histories->first()->to_status);
        $this->assertSame(PaymentStatus::Pending, $histories->last()->to_status);
    }

    public function test_payment_status_history_can_be_viewed_in_admin(): void
    {
        $admin = User::factory()->admin()->create();
        $payment = Payment::factory()->create(['status' => PaymentStatus::Unpaid]);

        $payment->markAsPaid();

        $this->actingAs($admin)
            ->get(route('filament.admin.resources.payments.view', $payment))
            ->assertOk()
            ->assertSee('Riwayat Perubahan Status');
    }

    public function test_operator_can_trigger_audit_trail(): void
    {
        $operator = User::factory()->create(['role' => UserRole::Operator]);
        $payment = Payment::factory()->create(['status' => PaymentStatus::Unpaid]);

        $this->actingAs($operator);
        $payment->markAsPending();

        $history = PaymentStatusHistory::where('payment_id', $payment->id)->firstOrFail();

        $this->assertSame($operator->id, $history->changed_by);
        $this->assertSame(PaymentStatus::Pending, $history->to_status);
    }

    public function test_manual_status_update_uses_default_reason_and_fills_paid_at(): void
    {
        $payment = Payment::factory()->create([
            'status' => PaymentStatus::Unpaid,
            'paid_at' => null,
        ]);

        $payment->update(['status' => PaymentStatus::Paid]);

        $payment->refresh();
        $history = PaymentStatusHistory::where('payment_id', $payment->id)->firstOrFail();

        $this->assertNotNull($payment->paid_at);
        $this->assertSame('Perubahan status manual', $history->reason);
        $this->assertSame(PaymentStatus::Paid, $history->to_status);
    }
}
