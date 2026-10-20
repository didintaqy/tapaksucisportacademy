<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Student;
use App\Models\TrainingSchedule;
use App\Models\User;
use Database\Seeders\StudentsTableSeeder;
use Database\Seeders\TrainingScheduleSeeder;
use Database\Seeders\UsersTableSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(UsersTableSeeder::class);
        $this->seed(TrainingScheduleSeeder::class);
        $this->seed(StudentsTableSeeder::class);
    }

    public function test_admin_can_view_attendance_list(): void
    {
        $admin = User::where('role', 'admin')->first();
        $this->actingAs($admin);

        $this->get(route('filament.admin.resources.attendances.index'))
            ->assertOk()
            ->assertSee('Absensi');
    }

    public function test_attendance_creation(): void
    {
        $student = Student::first();
        $schedule = TrainingSchedule::first();
        $user = User::where('role', 'admin')->first();

        $attendance = Attendance::create([
            'student_id' => $student->id,
            'training_schedule_id' => $schedule->id,
            'date' => now()->format('Y-m-d'),
            'status' => 'hadir',
            'notes' => 'Test attendance',
            'recorded_by' => $user->id,
        ]);

        $this->assertDatabaseHas('attendances', [
            'id' => $attendance->id,
            'student_id' => $student->id,
            'status' => 'hadir',
        ]);
    }

    public function test_attendance_unique_constraint(): void
    {
        $student = Student::first();
        $schedule = TrainingSchedule::first();
        $user = User::where('role', 'admin')->first();

        Attendance::create([
            'student_id' => $student->id,
            'training_schedule_id' => $schedule->id,
            'date' => now()->format('Y-m-d'),
            'status' => 'hadir',
            'recorded_by' => $user->id,
        ]);

        $this->expectException(QueryException::class);
        Attendance::create([
            'student_id' => $student->id,
            'training_schedule_id' => $schedule->id,
            'date' => now()->format('Y-m-d'),
            'status' => 'izin',
            'recorded_by' => $user->id,
        ]);
    }

    public function test_attendance_status_accessors(): void
    {
        $attendance = Attendance::factory()->make(['status' => 'hadir']);
        $this->assertEquals('Hadir', $attendance->status_label);
        $this->assertEquals('success', $attendance->status_color);

        $attendance->status = 'izin';
        $this->assertEquals('Izin', $attendance->status_label);
        $this->assertEquals('info', $attendance->status_color);

        $attendance->status = 'sakit';
        $this->assertEquals('Sakit', $attendance->status_label);
        $this->assertEquals('warning', $attendance->status_color);

        $attendance->status = 'alpa';
        $this->assertEquals('Alpa', $attendance->status_label);
        $this->assertEquals('danger', $attendance->status_color);
    }
}
