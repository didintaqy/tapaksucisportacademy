<?php

namespace Tests\Feature;

use App\Enums\PaymentPeriodType;
use App\Enums\UserRole;
use App\Models\Payment;
use App\Models\PaymentType;
use App\Models\Program;
use App\Models\Student;
use App\Models\TrainingSchedule;
use App\Models\User;
use App\Services\Payments\MonthlyBillGenerator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ProgramTest extends TestCase
{
    use RefreshDatabase;

    public function test_slug_is_generated_from_name(): void
    {
        $program = Program::create(['name' => 'Kelas Dasar Tapak Suci']);

        $this->assertSame('kelas-dasar-tapak-suci', $program->slug);
    }

    public function test_program_index_lists_only_active_programs(): void
    {
        Program::factory()->create(['name' => 'Kelas Aktif']);
        Program::factory()->inactive()->create(['name' => 'Kelas Nonaktif']);

        $this->get(route('program'))
            ->assertOk()
            ->assertSee('Kelas Aktif')
            ->assertDontSee('Kelas Nonaktif');
    }

    public function test_program_index_can_be_filtered_by_branch(): void
    {
        Program::factory()->create([
            'name' => 'Program Pusat',
            'branches' => ['Cabang Pusat'],
        ]);
        Program::factory()->create([
            'name' => 'Program Batam',
            'branches' => ['Cabang Batam Kota'],
        ]);

        $this->get(route('program', ['branch' => 'Cabang Batam Kota']))
            ->assertOk()
            ->assertSee('Program Batam')
            ->assertDontSee('Program Pusat');
    }

    public function test_program_detail_displays_content_and_schedule(): void
    {
        $program = Program::factory()->create([
            'name' => 'Kelas Remaja',
            'slug' => 'kelas-remaja',
            'description' => '<p>Materi latihan remaja.</p>',
            'branches' => ['Cabang Pusat'],
        ]);

        TrainingSchedule::factory()->create([
            'program_id' => $program->id,
            'class_name' => 'Kelas Remaja',
            'day_of_week' => 3,
            'start_time' => '16:00',
            'end_time' => '17:30',
            'is_active' => true,
        ]);

        $this->get(route('program.show', $program->slug))
            ->assertOk()
            ->assertSee('Kelas Remaja')
            ->assertSee('Materi latihan remaja.', false)
            ->assertSee('Rabu');
    }

    public function test_inactive_program_detail_returns_not_found(): void
    {
        $program = Program::factory()->inactive()->create();

        $this->get(route('program.show', $program->slug))->assertNotFound();
    }

    public function test_registration_stores_selected_program(): void
    {
        Mail::fake();

        $program = Program::factory()->create();

        $this->post(route('registration.store'), [
            'full_name' => 'Peserta Baru',
            'gender' => 'L',
            'birth_date' => '2010-01-01',
            'parent_name' => 'Orang Tua',
            'phone' => '081234567890',
            'address' => 'Jl. Contoh No. 1',
            'program_id' => $program->id,
        ])->assertRedirect(route('registration'));

        $this->assertDatabaseHas('student_registrations', [
            'full_name' => 'Peserta Baru',
            'program_id' => $program->id,
        ]);
    }

    public function test_monthly_bill_uses_program_fee_when_available(): void
    {
        $type = PaymentType::factory()->monthly()->create(['default_amount' => 150000]);
        $program = Program::factory()->create(['monthly_fee' => 275000]);
        $student = Student::factory()->create(['program_id' => $program->id]);

        app(MonthlyBillGenerator::class)->generate($type, now()->startOfMonth());

        $payment = Payment::query()->where('student_id', $student->id)->firstOrFail();

        $this->assertSame('275000.00', $payment->amount);
    }

    public function test_monthly_bill_falls_back_to_payment_type_amount(): void
    {
        $type = PaymentType::factory()->monthly()->create(['default_amount' => 150000]);
        $program = Program::factory()->create(['monthly_fee' => null]);
        $student = Student::factory()->create(['program_id' => $program->id]);

        app(MonthlyBillGenerator::class)->generate($type, now()->startOfMonth());

        $payment = Payment::query()->where('student_id', $student->id)->firstOrFail();

        $this->assertSame('150000.00', $payment->amount);
    }

    public function test_one_time_bill_ignores_program_fee(): void
    {
        $type = PaymentType::factory()->create([
            'default_amount' => 150000,
            'period_type' => PaymentPeriodType::OneTime,
        ]);
        $program = Program::factory()->create(['monthly_fee' => 275000]);
        $student = Student::factory()->create(['program_id' => $program->id]);

        app(MonthlyBillGenerator::class)->generate($type, now()->startOfMonth());

        $payment = Payment::query()->where('student_id', $student->id)->firstOrFail();

        $this->assertSame('150000.00', $payment->amount);
    }

    public function test_program_permissions_follow_role(): void
    {
        $admin = User::factory()->admin()->create();
        $operator = User::factory()->create(['role' => UserRole::Operator]);
        $coach = User::factory()->create(['role' => UserRole::Pelatih]);
        $program = Program::factory()->create();

        $this->assertTrue($admin->can('viewAny', Program::class));
        $this->assertTrue($admin->can('delete', $program));

        $this->assertTrue($operator->can('viewAny', Program::class));
        $this->assertTrue($operator->can('update', $program));
        $this->assertFalse($operator->can('delete', $program));

        $this->assertFalse($coach->can('viewAny', Program::class));
        $this->assertFalse($coach->can('update', $program));
    }

    public function test_program_resource_pages_load_for_admin(): void
    {
        $this->actingAs(User::factory()->admin()->create());

        $program = Program::factory()->create();

        $this->get('/admin/programs')->assertOk();
        $this->get('/admin/programs/create')->assertOk();
        $this->get("/admin/programs/{$program->slug}/edit")->assertOk();
    }
}
