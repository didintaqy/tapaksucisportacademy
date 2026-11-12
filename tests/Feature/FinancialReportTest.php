<?php

namespace Tests\Feature;

use App\Enums\PaymentStatus;
use App\Enums\UserRole;
use App\Filament\Pages\IncomeReport;
use App\Filament\Pages\OutstandingReport;
use App\Models\Payment;
use App\Models\PaymentType;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FinancialReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_income_report(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(IncomeReport::getUrl())
            ->assertOk()
            ->assertSee('Rekap Pemasukan')
            ->assertSee('Export CSV');
    }

    public function test_pelatih_cannot_open_income_report(): void
    {
        $pelatih = User::factory()->create(['role' => UserRole::Pelatih]);

        $this->actingAs($pelatih)
            ->get(IncomeReport::getUrl())
            ->assertForbidden();
    }

    public function test_income_report_shows_paid_payments_only(): void
    {
        $admin = User::factory()->admin()->create();
        $type = PaymentType::factory()->create(['name' => 'Iuran Bulanan', 'slug' => 'iuran-bulanan']);

        Payment::factory()->paid()->create([
            'payment_type_id' => $type->id,
            'amount' => 150000,
            'paid_at' => now(),
        ]);

        Payment::factory()->create([
            'payment_type_id' => $type->id,
            'amount' => 999000,
            'status' => PaymentStatus::Unpaid,
        ]);

        $this->actingAs($admin)
            ->get(IncomeReport::getUrl())
            ->assertOk()
            ->assertSee('Iuran Bulanan')
            ->assertSee('150.000')
            ->assertDontSee('999.000');
    }

    public function test_income_csv_export_contains_rows(): void
    {
        $admin = User::factory()->admin()->create();
        $student = Student::factory()->create(['full_name' => 'Budi Santoso']);
        $type = PaymentType::factory()->create(['name' => 'Iuran Bulanan', 'slug' => 'iuran-bulanan']);

        Payment::factory()->paid()->create([
            'student_id' => $student->id,
            'payment_type_id' => $type->id,
            'amount' => 150000,
            'paid_at' => now(),
        ]);

        $response = $this->actingAs($admin)->get(route('admin.reports.income.export', [
            'from' => now()->startOfMonth()->toDateString(),
            'to' => now()->toDateString(),
        ]));

        $response->assertOk();
        $this->assertStringContainsString('text/csv', $response->headers->get('Content-Type'));

        $content = $response->streamedContent();

        $this->assertStringContainsString('Budi Santoso', $content);
        $this->assertStringContainsString('Iuran Bulanan', $content);
        $this->assertStringContainsString('150000.00', $content);
    }

    public function test_income_excel_export_returns_xlsx(): void
    {
        $admin = User::factory()->admin()->create();
        Payment::factory()->paid()->create(['paid_at' => now(), 'amount' => 100000]);

        $response = $this->actingAs($admin)->get(route('admin.reports.income.excel'));

        $response->assertOk();
        $this->assertStringContainsString('spreadsheetml', $response->headers->get('Content-Type'));
        $this->assertStringStartsWith('PK', $response->streamedContent());
    }

    public function test_outstanding_excel_export_returns_xlsx(): void
    {
        $admin = User::factory()->admin()->create();
        Payment::factory()->create(['status' => PaymentStatus::Unpaid]);

        $response = $this->actingAs($admin)->get(route('admin.reports.outstanding.excel'));

        $response->assertOk();
        $this->assertStringContainsString('spreadsheetml', $response->headers->get('Content-Type'));
        $this->assertStringStartsWith('PK', $response->streamedContent());
    }

    public function test_income_pdf_export_returns_pdf(): void
    {
        $admin = User::factory()->admin()->create();
        Payment::factory()->paid()->create(['paid_at' => now(), 'amount' => 100000]);

        $response = $this->actingAs($admin)->get(route('admin.reports.income.pdf'));

        $response->assertOk();
        $this->assertStringContainsString('application/pdf', $response->headers->get('Content-Type'));
    }

    public function test_admin_can_open_outstanding_report(): void
    {
        $admin = User::factory()->admin()->create();
        $student = Student::factory()->create(['full_name' => 'Siti Aminah']);

        Payment::factory()->create([
            'student_id' => $student->id,
            'amount' => 250000,
            'status' => PaymentStatus::Unpaid,
            'due_date' => now()->subDays(10),
        ]);

        $this->actingAs($admin)
            ->get(OutstandingReport::getUrl())
            ->assertOk()
            ->assertSee('Laporan Tunggakan')
            ->assertSee('Siti Aminah')
            ->assertSee('250.000');
    }

    public function test_outstanding_csv_export_contains_row(): void
    {
        $admin = User::factory()->admin()->create();
        $student = Student::factory()->create(['full_name' => 'Siti Aminah']);

        Payment::factory()->create([
            'student_id' => $student->id,
            'amount' => 250000,
            'status' => PaymentStatus::Unpaid,
            'due_date' => now()->subDays(10),
        ]);

        $response = $this->actingAs($admin)->get(route('admin.reports.outstanding.export'));

        $response->assertOk();
        $content = $response->streamedContent();

        $this->assertStringContainsString('Siti Aminah', $content);
        $this->assertStringContainsString('250000.00', $content);
    }

    public function test_outstanding_pdf_export_returns_pdf(): void
    {
        $admin = User::factory()->admin()->create();
        Payment::factory()->create(['status' => PaymentStatus::Unpaid]);

        $response = $this->actingAs($admin)->get(route('admin.reports.outstanding.pdf'));

        $response->assertOk();
        $this->assertStringContainsString('application/pdf', $response->headers->get('Content-Type'));
    }

    public function test_pelatih_cannot_export_reports(): void
    {
        $pelatih = User::factory()->create(['role' => UserRole::Pelatih]);

        $this->actingAs($pelatih)
            ->get(route('admin.reports.income.export'))
            ->assertForbidden();

        $this->actingAs($pelatih)
            ->get(route('admin.reports.outstanding.pdf'))
            ->assertForbidden();
    }

    public function test_admin_can_queue_financial_report_export_and_receive_notification(): void
    {
        Storage::fake('public');

        $admin = User::factory()->admin()->create();
        $student = Student::factory()->create(['full_name' => 'Rizky Pratama']);
        $type = PaymentType::factory()->create(['name' => 'SPP Reguler', 'slug' => 'spp-reguler']);

        Payment::factory()->paid()->create([
            'student_id' => $student->id,
            'payment_type_id' => $type->id,
            'amount' => 175000,
            'paid_at' => now(),
        ]);

        $this->actingAs($admin)
            ->from(IncomeReport::getUrl())
            ->post(route('admin.reports.queue-export'), [
                'report_type' => 'income',
                'from' => now()->startOfMonth()->toDateString(),
                'to' => now()->toDateString(),
            ])
            ->assertRedirect(IncomeReport::getUrl());

        $this->assertDatabaseHas('notifications', [
            'notifiable_type' => User::class,
            'notifiable_id' => $admin->id,
        ]);
    }
}
