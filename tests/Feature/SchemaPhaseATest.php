<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Branch;
use App\Models\Event;
use App\Models\Payment;
use App\Models\PaymentType;
use App\Models\Student;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Fase A — integritas skema: cabang, constraint pembayaran, audit log,
 * dan konversi zona waktu tampilan.
 */
class SchemaPhaseATest extends TestCase
{
    use RefreshDatabase;

    public function test_two_payment_types_can_share_the_same_period(): void
    {
        $student = Student::factory()->create();
        $spp = PaymentType::factory()->create();
        $exam = PaymentType::factory()->create();

        $first = Payment::factory()->create([
            'student_id' => $student->id,
            'payment_type_id' => $spp->id,
            'period' => '2026-11',
        ]);

        $second = Payment::factory()->create([
            'student_id' => $student->id,
            'payment_type_id' => $exam->id,
            'period' => '2026-11',
        ]);

        $this->assertTrue($first->exists);
        $this->assertTrue($second->exists);
        $this->assertNotSame($first->id, $second->id);
    }

    public function test_duplicate_payment_for_same_type_and_period_is_rejected(): void
    {
        $student = Student::factory()->create();
        $type = PaymentType::factory()->create();

        Payment::factory()->create([
            'student_id' => $student->id,
            'payment_type_id' => $type->id,
            'period' => '2026-11',
        ]);

        $this->expectException(QueryException::class);

        Payment::factory()->create([
            'student_id' => $student->id,
            'payment_type_id' => $type->id,
            'period' => '2026-11',
        ]);
    }

    public function test_one_time_payments_without_period_are_not_limited(): void
    {
        $student = Student::factory()->create();
        $type = PaymentType::factory()->create();

        Payment::factory()->count(2)->create([
            'student_id' => $student->id,
            'payment_type_id' => $type->id,
            'period' => null,
        ]);

        $this->assertSame(2, Payment::where('student_id', $student->id)->count());
    }

    public function test_branch_string_is_mirrored_to_branch_id(): void
    {
        $student = Student::factory()->create(['branch' => 'Cabang Pusat']);

        $branch = Branch::where('slug', 'cabang-pusat')->first();

        $this->assertNotNull($branch);
        $this->assertSame($branch->id, $student->branch_id);
        $this->assertSame('Cabang Pusat', $student->branchRecord?->name);
    }

    public function test_blank_branch_does_not_create_a_branch_record(): void
    {
        $before = Branch::count();

        Student::factory()->create(['branch' => null]);

        $this->assertSame($before, Branch::count());
    }

    public function test_student_creation_and_update_are_audited(): void
    {
        $student = Student::factory()->create(['nickname' => 'Awal']);
        $student->update(['nickname' => 'Baru']);

        $created = ActivityLog::where('subject_type', Student::class)
            ->where('subject_id', $student->id)
            ->where('event', 'created')
            ->first();

        $updated = ActivityLog::where('subject_type', Student::class)
            ->where('subject_id', $student->id)
            ->where('event', 'updated')
            ->first();

        $this->assertNotNull($created);
        $this->assertNotNull($updated);
        $this->assertSame('Awal', data_get($updated->properties, 'old.nickname'));
        $this->assertSame('Baru', data_get($updated->properties, 'attributes.nickname'));
    }

    public function test_event_times_are_converted_to_display_timezone(): void
    {
        config(['app.display_timezone' => 'Asia/Jakarta']);

        $event = Event::create([
            'title' => 'Latihan Gabungan',
            'starts_at' => '2026-11-20 12:00:00',
            'type' => 'latihan',
            'is_public' => true,
        ]);

        $this->assertSame('19:00', $event->localStartsAt()?->format('H:i'));
        $this->assertSame('2026-11-20', $event->localStartsAt()?->format('Y-m-d'));
    }
}
