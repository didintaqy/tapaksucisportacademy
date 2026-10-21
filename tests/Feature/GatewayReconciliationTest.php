<?php

namespace Tests\Feature;

use App\Enums\PaymentStatus;
use App\Enums\UserRole;
use App\Filament\Pages\GatewayReconciliation;
use App\Models\Payment;
use App\Models\PaymentStatusHistory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class GatewayReconciliationTest extends TestCase
{
    use RefreshDatabase;

    private function gatewayPayment(array $overrides = []): Payment
    {
        return Payment::factory()->create(array_merge([
            'status' => PaymentStatus::Pending,
            'gateway_name' => 'midtrans',
            'gateway_reference' => 'trx-123',
            'gateway_payload' => [
                'last_callback' => [
                    'transaction_status' => 'settlement',
                    'transaction_time' => now()->toDateTimeString(),
                ],
            ],
        ], $overrides));
    }

    public function test_admin_can_open_reconciliation_page(): void
    {
        $admin = User::factory()->admin()->create();
        $payment = $this->gatewayPayment();

        $this->actingAs($admin)
            ->get(GatewayReconciliation::getUrl())
            ->assertOk()
            ->assertSee('Rekonsiliasi Pembayaran Gateway')
            ->assertSee($payment->invoice_number)
            ->assertSee('Selaraskan');
    }

    public function test_only_mismatch_filter_hides_synced_rows(): void
    {
        $admin = User::factory()->admin()->create();
        $mismatch = $this->gatewayPayment();
        $synced = $this->gatewayPayment(['status' => PaymentStatus::Paid]);

        $this->actingAs($admin)
            ->get(GatewayReconciliation::getUrl(['onlyMismatch' => 1]))
            ->assertOk()
            ->assertSee($mismatch->invoice_number)
            ->assertDontSee($synced->invoice_number);
    }

    public function test_pelatih_cannot_open_reconciliation_page(): void
    {
        $pelatih = User::factory()->create(['role' => UserRole::Pelatih]);

        $this->actingAs($pelatih)
            ->get(GatewayReconciliation::getUrl())
            ->assertForbidden();
    }

    public function test_mismatch_detection(): void
    {
        $this->assertTrue($this->gatewayPayment()->hasGatewayMismatch());

        $this->assertFalse($this->gatewayPayment([
            'status' => PaymentStatus::Paid,
        ])->hasGatewayMismatch());

        $this->assertFalse(Payment::factory()->create([
            'status' => PaymentStatus::Unpaid,
            'gateway_payload' => null,
        ])->hasGatewayMismatch());
    }

    public function test_reconcile_single_payment_updates_status_and_audit_trail(): void
    {
        $admin = User::factory()->admin()->create();
        $payment = $this->gatewayPayment();

        $this->actingAs($admin);

        Livewire::test(GatewayReconciliation::class)
            ->call('reconcile', $payment->id);

        $this->assertSame(PaymentStatus::Paid, $payment->fresh()->status);
        $this->assertDatabaseHas('payment_status_histories', [
            'payment_id' => $payment->id,
            'to_status' => PaymentStatus::Paid->value,
            'reason' => 'Rekonsiliasi manual dari halaman admin',
        ]);
    }

    public function test_reconcile_all_updates_every_mismatch(): void
    {
        $admin = User::factory()->admin()->create();
        $first = $this->gatewayPayment();
        $second = $this->gatewayPayment();
        $paid = $this->gatewayPayment(['status' => PaymentStatus::Paid]);

        $this->actingAs($admin);

        Livewire::test(GatewayReconciliation::class)
            ->call('reconcileAll');

        $this->assertSame(PaymentStatus::Paid, $first->fresh()->status);
        $this->assertSame(PaymentStatus::Paid, $second->fresh()->status);
        $this->assertSame(PaymentStatus::Paid, $paid->fresh()->status);

        $this->assertSame(0, PaymentStatusHistory::where('payment_id', $paid->id)->count());
    }

    public function test_command_dry_run_does_not_change_status(): void
    {
        $payment = $this->gatewayPayment();

        $this->artisan('payments:reconcile-gateway')
            ->expectsOutputToContain('Rekonsiliasi selesai')
            ->assertSuccessful();

        $this->assertSame(PaymentStatus::Pending, $payment->fresh()->status);
    }

    public function test_command_apply_changes_status_with_audit_trail(): void
    {
        $payment = $this->gatewayPayment();

        $this->artisan('payments:reconcile-gateway --apply')
            ->expectsOutputToContain('1 diperbarui')
            ->assertSuccessful();

        $this->assertSame(PaymentStatus::Paid, $payment->fresh()->status);
        $this->assertDatabaseHas('payment_status_histories', [
            'payment_id' => $payment->id,
            'to_status' => PaymentStatus::Paid->value,
            'reason' => 'Rekonsiliasi otomatis dengan payment gateway',
        ]);
    }
}
