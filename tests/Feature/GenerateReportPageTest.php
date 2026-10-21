<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Filament\Pages\GenerateReport;
use App\Models\Attendance;
use App\Models\Student;
use App\Models\StudentPerformance;
use App\Models\TrainingSchedule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class GenerateReportPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_report_page(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(GenerateReport::getUrl())
            ->assertOk()
            ->assertSee('Generate Rapor');
    }

    public function test_pelatih_cannot_open_report_page(): void
    {
        $pelatih = User::factory()->create(['role' => UserRole::Pelatih]);

        $this->actingAs($pelatih)
            ->get(GenerateReport::getUrl())
            ->assertForbidden();
    }

    public function test_student_code_is_generated_automatically(): void
    {
        $student = Student::factory()->create();

        $this->assertNotNull($student->student_code);
        $this->assertSame('SIS-'.str_pad((string) $student->id, 5, '0', STR_PAD_LEFT), $student->student_code);
    }

    public function test_report_page_mounts_with_default_state(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin);

        Livewire::test(GenerateReport::class)
            ->assertSet('data.format', 'pdf')
            ->assertSet(
                'data.date_from',
                fn ($value): bool => str_starts_with((string) $value, now()->startOfMonth()->format('Y-m-d')),
            );
    }

    public function test_admin_can_generate_pdf_report_with_attendance_and_performance(): void
    {
        $admin = User::factory()->admin()->create();
        $student = Student::factory()->create(['full_name' => 'Rizky Pratama']);
        $schedule = TrainingSchedule::factory()->create();

        Attendance::factory()->create([
            'student_id' => $student->id,
            'training_schedule_id' => $schedule->id,
            'date' => now()->toDateString(),
            'status' => 'hadir',
        ]);

        StudentPerformance::factory()->create([
            'student_id' => $student->id,
            'beep_test_vo2max' => 42.5,
            'push_up_1_min' => 30,
            'sit_up_2_min' => 40,
        ]);

        $this->actingAs($admin);

        Livewire::test(GenerateReport::class)
            ->set('data.student_id', $student->id)
            ->set('data.date_from', now()->startOfMonth()->toDateString())
            ->set('data.date_to', now()->toDateString())
            ->set('data.format', 'pdf')
            ->call('generateReport')
            ->assertFileDownloaded();
    }

    public function test_report_html_preview_is_dispatched(): void
    {
        $admin = User::factory()->admin()->create();
        $student = Student::factory()->create();

        $this->actingAs($admin);

        Livewire::test(GenerateReport::class)
            ->set('data.student_id', $student->id)
            ->set('data.date_from', now()->startOfMonth()->toDateString())
            ->set('data.date_to', now()->toDateString())
            ->set('data.format', 'html')
            ->call('generateReport')
            ->assertDispatched('showHtmlPreview');
    }
}
