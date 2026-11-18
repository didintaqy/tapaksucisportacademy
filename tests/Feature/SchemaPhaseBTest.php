<?php

namespace Tests\Feature;

use App\Enums\EnrollmentStatus;
use App\Models\Coach;
use App\Models\Enrollment;
use App\Models\Event;
use App\Models\EventParticipant;
use App\Models\NotificationLog;
use App\Models\Program;
use App\Models\Student;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Fase B — keanggotaan, penugasan pelatih, peserta event, log notifikasi.
 */
class SchemaPhaseBTest extends TestCase
{
    use RefreshDatabase;

    public function test_creating_student_with_program_creates_active_enrollment(): void
    {
        $program = Program::factory()->create();

        $student = Student::factory()->create(['program_id' => $program->id]);

        $enrollment = $student->activeEnrollment();

        $this->assertNotNull($enrollment);
        $this->assertSame($program->id, $enrollment->program_id);
        $this->assertTrue($enrollment->isActive());
    }

    public function test_only_one_active_enrollment_is_allowed_per_student(): void
    {
        $student = Student::factory()->create();
        $program = Program::factory()->create();

        Enrollment::create([
            'student_id' => $student->id,
            'program_id' => $program->id,
            'status' => EnrollmentStatus::Active,
            'started_at' => now()->toDateString(),
        ]);

        $this->expectException(QueryException::class);

        Enrollment::create([
            'student_id' => $student->id,
            'program_id' => $program->id,
            'status' => EnrollmentStatus::Active,
            'started_at' => now()->toDateString(),
        ]);
    }

    public function test_finished_enrollment_allows_a_new_active_one(): void
    {
        $student = Student::factory()->create();
        $first = Program::factory()->create();
        $second = Program::factory()->create();

        $old = Enrollment::create([
            'student_id' => $student->id,
            'program_id' => $first->id,
            'status' => EnrollmentStatus::Active,
            'started_at' => now()->subYear()->toDateString(),
        ]);

        $old->update([
            'status' => EnrollmentStatus::Berhenti,
            'ended_at' => now()->toDateString(),
        ]);

        $this->assertNull($old->fresh()->active_key);

        $new = Enrollment::create([
            'student_id' => $student->id,
            'program_id' => $second->id,
            'status' => EnrollmentStatus::Active,
            'started_at' => now()->toDateString(),
        ]);

        $this->assertTrue($new->exists);
        $this->assertSame($second->id, $student->fresh()->activeEnrollment()?->program_id);
    }

    public function test_coach_and_student_can_be_linked(): void
    {
        $coach = Coach::factory()->create();
        $student = Student::factory()->create();

        $student->coaches()->attach($coach->id, [
            'assigned_at' => now()->toDateString(),
        ]);

        $this->assertTrue($coach->fresh()->students->contains($student->id));
        $this->assertTrue($student->fresh()->coaches->contains($coach->id));
    }

    public function test_event_participant_is_unique_per_event_and_student(): void
    {
        $event = Event::factory()->create();
        $student = Student::factory()->create();

        EventParticipant::create([
            'event_id' => $event->id,
            'student_id' => $student->id,
            'status' => 'registered',
        ]);

        $this->assertTrue($event->fresh()->participants->contains($student->id));

        $this->expectException(QueryException::class);

        EventParticipant::create([
            'event_id' => $event->id,
            'student_id' => $student->id,
            'status' => 'registered',
        ]);
    }

    public function test_notification_log_records_delivery_status(): void
    {
        $log = NotificationLog::create([
            'channel' => 'email',
            'recipient' => 'wali@example.test',
            'template' => 'payment_reminder',
            'status' => 'queued',
        ]);

        $log->markAsSent('msg-123');

        $log->refresh();

        $this->assertSame('sent', $log->status);
        $this->assertSame('msg-123', $log->provider_ref);
        $this->assertNotNull($log->sent_at);
    }
}
