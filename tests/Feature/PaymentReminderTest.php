<?php

namespace Tests\Feature;

use App\Enums\PaymentStatus;
use App\Filament\Resources\Payments\Pages\ListPayments;
use App\Mail\PaymentReminderMail;
use App\Models\Payment;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

class PaymentReminderTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_sends_reminder_for_due_soon_payment(): void
    {
        Mail::fake();

        $student = Student::factory()->create(['email' => 'wali@example.test']);
        $payment = Payment::factory()->create([
            'student_id' => $student->id,
            'status' => PaymentStatus::Unpaid,
            'due_date' => now()->addDays(2),
            'reminded_at' => null,
        ]);

        $this->artisan('payments:send-reminders --days=3')
            ->assertSuccessful();

        Mail::assertQueued(PaymentReminderMail::class, fn (PaymentReminderMail $mail): bool => $mail->payment->is($payment));

        $payment->refresh();
        $this->assertNotNull($payment->reminded_at);
        $this->assertSame(1, $payment->reminder_count);
    }

    public function test_command_skips_paid_payment(): void
    {
        Mail::fake();

        $payment = Payment::factory()->paid()->create([
            'due_date' => now()->addDay(),
        ]);

        $this->artisan('payments:send-reminders')->assertSuccessful();

        Mail::assertNothingQueued();
        $this->assertNull($payment->fresh()->reminded_at);
    }

    public function test_command_skips_student_without_email(): void
    {
        Mail::fake();

        $student = Student::factory()->create(['email' => null]);
        Payment::factory()->create([
            'student_id' => $student->id,
            'status' => PaymentStatus::Unpaid,
            'due_date' => now()->addDay(),
        ]);

        $this->artisan('payments:send-reminders')->assertSuccessful();

        Mail::assertNothingQueued();
    }

    public function test_command_skips_payment_due_beyond_threshold(): void
    {
        Mail::fake();

        Payment::factory()->create([
            'status' => PaymentStatus::Unpaid,
            'due_date' => now()->addDays(10),
        ]);

        $this->artisan('payments:send-reminders --days=3')->assertSuccessful();

        Mail::assertNothingQueued();
    }

    public function test_dry_run_does_not_send_or_mark(): void
    {
        Mail::fake();

        $payment = Payment::factory()->create([
            'status' => PaymentStatus::Unpaid,
            'due_date' => now()->addDay(),
        ]);

        $this->artisan('payments:send-reminders --dry-run')
            ->expectsOutputToContain('dry-run')
            ->assertSuccessful();

        Mail::assertNothingQueued();
        $this->assertNull($payment->fresh()->reminded_at);
    }

    public function test_same_day_repeat_does_not_resend(): void
    {
        Mail::fake();

        $payment = Payment::factory()->create([
            'status' => PaymentStatus::Unpaid,
            'due_date' => now()->addDay(),
        ]);

        $this->artisan('payments:send-reminders')->assertSuccessful();
        $this->artisan('payments:send-reminders')->assertSuccessful();

        Mail::assertQueuedCount(1);

        $this->assertSame(1, $payment->fresh()->reminder_count);
    }

    public function test_reminder_mail_renders_invoice_details(): void
    {
        $student = Student::factory()->create(['full_name' => 'Ahmad Fauzi']);
        $payment = Payment::factory()->create([
            'student_id' => $student->id,
            'status' => PaymentStatus::Unpaid,
            'due_date' => now()->addDays(2),
            'amount' => 150000,
        ]);

        $mail = new PaymentReminderMail($payment, 2);
        $html = $mail->render();

        $this->assertStringContainsString('Pengingat Pembayaran Tagihan', $html);
        $this->assertStringContainsString($payment->invoice_number, $html);
        $this->assertStringContainsString('Ahmad Fauzi', $html);
        $this->assertStringContainsString('Rp 150.000', $html);
        $this->assertStringContainsString('2 hari', $html);
    }

    public function test_admin_can_send_reminder_from_payment_table_action(): void
    {
        Mail::fake();

        $admin = User::factory()->admin()->create();
        $student = Student::factory()->create(['email' => 'wali@example.test']);
        $payment = Payment::factory()->create([
            'student_id' => $student->id,
            'status' => PaymentStatus::Unpaid,
            'due_date' => now()->addDays(2),
        ]);

        $this->actingAs($admin);

        Livewire::test(ListPayments::class)
            ->callTableAction('send_reminder', $payment);

        Mail::assertQueued(
            PaymentReminderMail::class,
            fn (PaymentReminderMail $mail): bool => $mail->payment->is($payment),
        );

        $this->assertNotNull($payment->fresh()->reminded_at);
        $this->assertSame(1, $payment->fresh()->reminder_count);
    }

    public function test_bulk_reminder_skips_paid_payments(): void
    {
        Mail::fake();

        $admin = User::factory()->admin()->create();
        $outstanding = Payment::factory()->create([
            'student_id' => Student::factory()->create(['email' => 'wali-a@example.test'])->id,
            'status' => PaymentStatus::Unpaid,
            'due_date' => now()->addDays(2),
        ]);
        $paid = Payment::factory()->paid()->create([
            'student_id' => Student::factory()->create(['email' => 'wali-b@example.test'])->id,
            'due_date' => now()->addDays(2),
        ]);

        $this->actingAs($admin);

        Livewire::test(ListPayments::class)
            ->callTableBulkAction('send_reminders_bulk', [$outstanding->id, $paid->id]);

        Mail::assertQueuedCount(1);
        Mail::assertQueued(
            PaymentReminderMail::class,
            fn (PaymentReminderMail $mail): bool => $mail->payment->is($outstanding),
        );

        $this->assertNotNull($outstanding->fresh()->reminded_at);
        $this->assertNull($paid->fresh()->reminded_at);
    }
}
