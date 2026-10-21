<?php

namespace Tests\Feature;

use App\Enums\PaymentStatus;
use App\Enums\UserRole;
use App\Filament\Resources\Payments\PaymentResource;
use App\Filament\Widgets\DashboardStatsOverview;
use App\Filament\Widgets\DueBillsTable;
use App\Filament\Widgets\IncomeChart;
use App\Filament\Widgets\PerformanceOverview;
use App\Models\Payment;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_renders(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get('/admin')
            ->assertOk();
    }

    public function test_due_bills_widget_renders_records_and_links_to_payment_detail(): void
    {
        $admin = User::factory()->admin()->create();
        $student = Student::factory()->create();

        $payment = Payment::factory()->create([
            'student_id' => $student->id,
            'status' => PaymentStatus::Unpaid,
            'due_date' => now()->addDays(2),
            'amount' => 150000,
        ]);

        $this->actingAs($admin);

        Livewire::test(DueBillsTable::class)
            ->assertOk()
            ->assertCanSeeTableRecords([$payment])
            ->assertSee($student->full_name)
            ->assertSee(
                PaymentResource::getUrl('view', ['record' => $payment]),
                escape: false,
            );
    }

    public function test_financial_widgets_render_for_operator(): void
    {
        $operator = User::factory()->create(['role' => UserRole::Operator]);

        Payment::factory()->create([
            'status' => PaymentStatus::Unpaid,
            'due_date' => now()->addDay(),
        ]);

        $this->actingAs($operator);

        $this->assertTrue(DueBillsTable::canView());
        $this->assertTrue(IncomeChart::canView());

        Livewire::test(DashboardStatsOverview::class)
            ->assertOk()
            ->assertSee('Total Siswa');

        Livewire::test(IncomeChart::class)
            ->assertOk()
            ->assertSee('Pemasukan 6 Bulan Terakhir');

        Livewire::test(PerformanceOverview::class)
            ->assertOk()
            ->assertSee('Rata-rata VO2max');
    }

    public function test_pelatih_dashboard_hides_financial_widgets(): void
    {
        $pelatih = User::factory()->create(['role' => UserRole::Pelatih]);

        Payment::factory()->create([
            'status' => PaymentStatus::Unpaid,
            'due_date' => now()->addDay(),
        ]);

        $this->actingAs($pelatih)
            ->get('/admin')
            ->assertOk();

        $this->assertFalse(DueBillsTable::canView());
        $this->assertFalse(IncomeChart::canView());
        $this->assertFalse(DashboardStatsOverview::canView());
        $this->assertTrue(PerformanceOverview::canView());
    }
}
