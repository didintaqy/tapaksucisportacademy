<?php

namespace Tests\Feature;

use App\Enums\BeltRank;
use App\Enums\MedalType;
use App\Enums\PromotionResult;
use App\Models\Achievement;
use App\Models\Attendance;
use App\Models\BeltPromotion;
use App\Models\FitnessBenchmark;
use App\Models\FitnessComponent;
use App\Models\FitnessTestItem;
use App\Models\ScoreCategory;
use App\Models\Student;
use App\Models\StudentPerformance;
use App\Models\TrainingSchedule;
use App\Services\Fitness\PerformanceAnalysisService;
use Database\Seeders\FitnessBenchmarkSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FitnessBenchmarkAndPortalTest extends TestCase
{
    use RefreshDatabase;

    public function test_fitness_benchmark_seeder_populates_remaja_master_data_idempotently(): void
    {
        $this->seed(FitnessBenchmarkSeeder::class);
        $this->seed(FitnessBenchmarkSeeder::class);

        $this->assertSame(5, FitnessComponent::count());
        $this->assertSame(10, FitnessTestItem::count());
        $this->assertSame(5, ScoreCategory::count());
        $this->assertSame(80, FitnessBenchmark::count());
    }

    public function test_performance_analysis_service_computes_age_gender_benchmarks_and_score_category(): void
    {
        $this->seed(FitnessBenchmarkSeeder::class);

        $student = Student::factory()->create([
            'gender' => 'L',
            'birth_date' => now()->subYears(17)->toDateString(),
        ]);

        $performance = StudentPerformance::create([
            'student_id' => $student->id,
            'test_date' => now()->toDateString(),
            'resting_heart_rate' => 70,
            'max_heart_rate' => 203,
            'training_heart_rate' => 160,
            'height' => 1.65,
            'weight' => 58,
            'sit_and_reach' => 30,
            'push_up_1_min' => 35,
            'sit_up_2_min' => 50,
            'core_stability_level' => '12',
            'triple_hop_right' => 5.5,
            'triple_hop_left' => 5.5,
            'illinois_agility' => 16.0,
            'rast_average_power' => 450,
            'rast_fatigue_index' => 4.0,
            'beep_test_vo2max' => 50.0,
        ]);

        $analysis = app(PerformanceAnalysisService::class)->analyze($performance, $student);

        $this->assertSame(100, $analysis['overall_score']);
        $this->assertSame('Sangat Baik', $analysis['score_category']['label']);
        $this->assertSame('NORMAL', $analysis['bmi_category']);
        $this->assertCount(10, $analysis['items']);
        $this->assertSame(100, $analysis['pillar_scores']['Flexibility']);
        $this->assertSame(100, $analysis['pillar_scores']['Strength']);
        $this->assertSame(100, $analysis['pillar_scores']['Power']);
        $this->assertSame(100, $analysis['pillar_scores']['Agility']);
        $this->assertSame(100, $analysis['pillar_scores']['Endurance']);
    }

    public function test_student_portal_login_by_student_code_and_birth_date_opens_dashboard(): void
    {
        $this->seed(FitnessBenchmarkSeeder::class);

        $student = Student::factory()->create([
            'full_name' => 'Zahra Aulia',
            'birth_date' => '2012-04-15',
            'gender' => 'P',
            'belt_level' => BeltRank::SiswaMelati1->value,
        ]);

        StudentPerformance::create([
            'student_id' => $student->id,
            'test_date' => now()->subMonth()->toDateString(),
            'height' => 1.50,
            'weight' => 45,
            'sit_and_reach' => 24,
            'push_up_1_min' => 20,
            'sit_up_2_min' => 32,
            'core_stability_level' => '9',
            'triple_hop_right' => 3.6,
            'triple_hop_left' => 3.5,
            'illinois_agility' => 19.5,
            'rast_average_power' => 260,
            'rast_fatigue_index' => 5.2,
            'beep_test_vo2max' => 38,
        ]);

        Achievement::factory()->create([
            'student_id' => $student->id,
            'competition_name' => 'Kejuaraan Daerah 5 Batam',
            'medal' => MedalType::Emas,
        ]);

        $schedule = TrainingSchedule::factory()->create(['class_name' => 'Kelas Remaja']);
        Attendance::factory()->create([
            'student_id' => $student->id,
            'training_schedule_id' => $schedule->id,
            'date' => now()->subDays(2)->toDateString(),
            'status' => 'hadir',
        ]);

        $this->post(route('student.portal.login'), [
            'student_id' => $student->student_code,
            'birth_date' => '2012-04-15',
        ])->assertRedirect(route('student.portal.dashboard'));

        $this->get(route('student.portal.dashboard'))
            ->assertOk()
            ->assertSee('Zahra Aulia')
            ->assertSee('Profil Kebugaran Jasmani (REMAJA)')
            ->assertSee('Kejuaraan Daerah 5 Batam')
            ->assertSee('Kelas Remaja');
    }

    public function test_student_portal_rejects_wrong_birth_date_with_uniform_message(): void
    {
        $student = Student::factory()->create(['birth_date' => '2012-04-15']);

        $wrongDate = $this->from(route('student.portal'))->post(route('student.portal.login'), [
            'student_id' => $student->student_code,
            'birth_date' => '2000-01-01',
        ]);

        $unknown = $this->from(route('student.portal'))->post(route('student.portal.login'), [
            'student_id' => 'SIS-99999',
            'birth_date' => '2000-01-01',
        ]);

        $wrongDate->assertRedirect(route('student.portal'));
        $this->assertSame(
            $wrongDate->getSession()->get('error'),
            $unknown->getSession()->get('error'),
        );
    }

    public function test_student_portal_can_download_report_pdf_and_own_certificate(): void
    {
        $student = Student::factory()->create(['birth_date' => '2011-08-10']);
        $otherStudent = Student::factory()->create(['birth_date' => '2011-08-10']);

        $ownPromo = BeltPromotion::factory()->create([
            'student_id' => $student->id,
            'result' => PromotionResult::Lulus,
            'certificate_number' => 'UKT-2026-0001',
        ]);

        $otherPromo = BeltPromotion::factory()->create([
            'student_id' => $otherStudent->id,
            'result' => PromotionResult::Lulus,
            'certificate_number' => 'UKT-2026-0002',
        ]);

        $pdfResponse = $this->withSession(['student_portal_student_id' => $student->id])
            ->get(route('student.portal.report-pdf'));

        $pdfResponse->assertOk();
        $this->assertStringContainsString('application/pdf', $pdfResponse->headers->get('Content-Type'));

        $this->withSession(['student_portal_student_id' => $student->id])
            ->get(route('student.portal.certificate', $ownPromo))
            ->assertOk();

        $this->withSession(['student_portal_student_id' => $student->id])
            ->get(route('student.portal.certificate', $otherPromo))
            ->assertNotFound();
    }
}
