<?php

namespace Tests\Feature;

use App\Enums\PromotionResult;
use App\Enums\UserRole;
use App\Models\Attendance;
use App\Models\BeltPromotion;
use App\Models\Student;
use App\Models\TrainingSchedule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CoachDataScopeTest extends TestCase
{
    use RefreshDatabase;

    public function test_coach_can_only_view_and_update_attendance_for_own_schedule(): void
    {
        $coach = User::factory()->create(['role' => UserRole::Pelatih]);
        $otherCoach = User::factory()->create(['role' => UserRole::Pelatih]);

        $ownSchedule = TrainingSchedule::factory()->create([
            'coach_id' => $coach->id,
            'branch' => 'Cabang Pusat',
            'class_name' => 'Kelas Pusat',
        ]);
        $otherSchedule = TrainingSchedule::factory()->create([
            'coach_id' => $otherCoach->id,
            'branch' => 'Cabang Batam Kota',
            'class_name' => 'Kelas Batam',
        ]);

        $ownStudent = Student::factory()->create([
            'full_name' => 'Siswa Jadwal Sendiri',
            'branch' => 'Cabang Pusat',
        ]);
        $otherStudent = Student::factory()->create([
            'full_name' => 'Siswa Jadwal Lain',
            'branch' => 'Cabang Batam Kota',
        ]);

        $ownAttendance = Attendance::factory()->create([
            'student_id' => $ownStudent->id,
            'training_schedule_id' => $ownSchedule->id,
            'recorded_by' => $coach->id,
        ]);
        $otherAttendance = Attendance::factory()->create([
            'student_id' => $otherStudent->id,
            'training_schedule_id' => $otherSchedule->id,
            'recorded_by' => $otherCoach->id,
        ]);

        $this->assertTrue($coach->can('view', $ownAttendance));
        $this->assertTrue($coach->can('update', $ownAttendance));
        $this->assertFalse($coach->can('view', $otherAttendance));
        $this->assertFalse($coach->can('update', $otherAttendance));

        $this->actingAs($coach)
            ->get('/admin/attendances')
            ->assertOk()
            ->assertSee('Siswa Jadwal Sendiri')
            ->assertDontSee('Siswa Jadwal Lain');
    }

    public function test_coach_can_only_view_and_update_belt_promotions_for_own_branch(): void
    {
        $coach = User::factory()->create(['role' => UserRole::Pelatih]);
        $otherCoach = User::factory()->create(['role' => UserRole::Pelatih]);

        TrainingSchedule::factory()->create([
            'coach_id' => $coach->id,
            'branch' => 'Cabang Pusat',
        ]);
        TrainingSchedule::factory()->create([
            'coach_id' => $otherCoach->id,
            'branch' => 'Cabang Batam Kota',
        ]);

        $ownStudent = Student::factory()->create([
            'full_name' => 'Siswa UKT Sendiri',
            'branch' => 'Cabang Pusat',
        ]);
        $otherStudent = Student::factory()->create([
            'full_name' => 'Siswa UKT Lain',
            'branch' => 'Cabang Batam Kota',
        ]);

        $ownPromotion = BeltPromotion::factory()->lulus()->forStudent($ownStudent)->create();
        $otherPromotion = BeltPromotion::factory()->lulus()->forStudent($otherStudent)->create();

        $this->assertTrue($coach->can('view', $ownPromotion));
        $this->assertTrue($coach->can('update', $ownPromotion));
        $this->assertFalse($coach->can('view', $otherPromotion));
        $this->assertFalse($coach->can('update', $otherPromotion));

        $this->actingAs($coach)
            ->get('/admin/belt-promotions')
            ->assertOk()
            ->assertSee('Siswa UKT Sendiri')
            ->assertDontSee('Siswa UKT Lain');
    }

    public function test_attendance_recorder_is_filled_automatically(): void
    {
        $coach = User::factory()->create(['role' => UserRole::Pelatih]);
        $schedule = TrainingSchedule::factory()->create(['coach_id' => $coach->id]);
        $student = Student::factory()->create(['branch' => $schedule->branch]);

        $this->actingAs($coach);

        $attendance = Attendance::create([
            'student_id' => $student->id,
            'training_schedule_id' => $schedule->id,
            'date' => now()->toDateString(),
            'status' => 'hadir',
        ]);

        $this->assertSame($coach->id, $attendance->recorded_by);
    }

    public function test_passed_belt_promotion_gets_certificate_number_automatically(): void
    {
        $student = Student::factory()->create();
        $examiner = User::factory()->create(['role' => UserRole::Pelatih]);

        $promotion = BeltPromotion::create([
            'student_id' => $student->id,
            'examined_by' => $examiner->id,
            'exam_date' => now()->toDateString(),
            'current_belt' => 'putih',
            'target_belt' => 'kuning',
            'result' => PromotionResult::Lulus,
        ]);

        $this->assertNotNull($promotion->certificate_number);
        $this->assertStringStartsWith('UKT-', $promotion->certificate_number);
        $this->assertNotNull($promotion->certificate_issued_at);
    }
}
