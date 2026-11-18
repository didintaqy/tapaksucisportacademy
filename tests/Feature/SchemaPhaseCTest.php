<?php

namespace Tests\Feature;

use App\Contracts\WhatsAppGateway;
use App\Models\Attendance;
use App\Models\Coach;
use App\Models\CoachRate;
use App\Models\Guardian;
use App\Models\Item;
use App\Models\NotificationLog;
use App\Models\StockMovement;
use App\Models\Student;
use App\Models\TrainingSchedule;
use App\Services\Payroll\CoachPayrollCalculator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Fase C — guardian, QR check-in, inventaris, payroll, WhatsApp.
 */
class SchemaPhaseCTest extends TestCase
{
    use RefreshDatabase;

    public function test_guardian_can_be_linked_to_multiple_students(): void
    {
        $guardian = Guardian::create([
            'name' => 'Budi Santoso',
            'phone' => '081200000000',
        ]);

        $first = Student::factory()->create();
        $second = Student::factory()->create();

        $guardian->students()->attach($first->id, ['relation' => 'ayah', 'is_primary' => true]);
        $guardian->students()->attach($second->id, ['relation' => 'ayah']);

        $this->assertSame(2, $guardian->fresh()->students->count());
        $this->assertTrue($first->fresh()->guardians->contains($guardian->id));
        $this->assertSame($first->id, $guardian->fresh()->primaryStudent()?->id);
    }

    public function test_student_gets_a_unique_qr_token(): void
    {
        $first = Student::factory()->create();
        $second = Student::factory()->create();

        $this->assertNotEmpty($first->qr_token);
        $this->assertSame(40, strlen($first->qr_token));
        $this->assertNotSame($first->qr_token, $second->qr_token);
    }

    public function test_attendance_can_be_checked_in(): void
    {
        $attendance = Attendance::factory()->create(['status' => 'alpa']);

        $attendance->markCheckedIn('qr');

        $attendance->refresh();

        $this->assertSame('hadir', $attendance->status);
        $this->assertSame('qr', $attendance->checkin_method);
        $this->assertTrue($attendance->isCheckedIn());
    }

    public function test_stock_movements_update_item_stock(): void
    {
        $item = Item::create([
            'code' => 'SRG-01',
            'name' => 'Seragam Tapak Suci',
            'category' => 'seragam',
            'stock' => 0,
            'min_stock' => 5,
        ]);

        StockMovement::create(['item_id' => $item->id, 'type' => 'in', 'quantity' => 10]);
        $this->assertSame(10, $item->fresh()->stock);

        StockMovement::create(['item_id' => $item->id, 'type' => 'out', 'quantity' => 4]);
        $this->assertSame(6, $item->fresh()->stock);

        StockMovement::create(['item_id' => $item->id, 'type' => 'adjustment', 'quantity' => 3]);
        $this->assertSame(3, $item->fresh()->stock);
    }

    public function test_payroll_is_calculated_from_sessions_and_rate(): void
    {
        $coach = Coach::factory()->create();

        CoachRate::create([
            'coach_id' => $coach->id,
            'rate_type' => 'per_session',
            'amount' => 50000,
            'effective_from' => '2026-01-01',
        ]);

        $schedule = TrainingSchedule::factory()->create(['coach_ref_id' => $coach->id]);

        Attendance::factory()->forSchedule($schedule)->create(['date' => '2026-11-05']);
        Attendance::factory()->forSchedule($schedule)->create(['date' => '2026-11-05']);
        Attendance::factory()->forSchedule($schedule)->create(['date' => '2026-11-12']);

        $calculator = app(CoachPayrollCalculator::class);

        $this->assertSame(2, $calculator->sessionsHeld($coach, '2026-11'));

        $payroll = $calculator->draftFor($coach, '2026-11');

        $this->assertSame(2, $payroll->sessions_count);
        $this->assertSame('100000.00', $payroll->total);
        $this->assertSame('draft', $payroll->status->value);
    }

    public function test_whatsapp_log_driver_records_notification(): void
    {
        $sent = app(WhatsAppGateway::class)->send('6281200000000', 'Halo orang tua', 'payment_reminder');

        $this->assertTrue($sent);
        $this->assertDatabaseHas('notification_logs', [
            'channel' => 'whatsapp',
            'recipient' => '6281200000000',
            'template' => 'payment_reminder',
            'status' => 'sent',
        ]);

        $this->assertSame(1, NotificationLog::where('channel', 'whatsapp')->count());
    }
}
