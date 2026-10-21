<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\BeltPromotion;
use App\Models\Payment;
use App\Models\Student;
use App\Models\TrainingSchedule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DocumentAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_open_certificate_or_receipt(): void
    {
        $promotion = BeltPromotion::factory()->lulus()->create();
        $payment = Payment::factory()->paid()->create();

        $this->get(route('belt-promotions.certificate', $promotion))->assertRedirect('/admin/login');
        $this->get(route('payments.receipt', $payment))->assertRedirect('/admin/login');
    }

    public function test_authorized_user_can_open_belt_promotion_certificate(): void
    {
        $promotion = BeltPromotion::factory()->lulus()->create([
            'certificate_number' => 'UKT-2026-0001',
        ]);

        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('belt-promotions.certificate', $promotion))
            ->assertOk()
            ->assertSee('UKT-2026-0001')
            ->assertSee($promotion->student->full_name)
            ->assertSee('Sertifikat');
    }

    public function test_coach_can_open_certificate_but_not_payment_receipt(): void
    {
        $coach = User::factory()->create(['role' => UserRole::Pelatih]);
        $schedule = TrainingSchedule::factory()->create(['coach_id' => $coach->id]);
        $student = Student::factory()->create(['branch' => $schedule->branch]);
        $promotion = BeltPromotion::factory()->lulus()->forStudent($student)->create();
        $payment = Payment::factory()->paid()->create();

        $this->actingAs($coach)
            ->get(route('belt-promotions.certificate', $promotion))
            ->assertOk();

        $this->actingAs($coach)
            ->get(route('payments.receipt', $payment))
            ->assertForbidden();
    }

    public function test_certificate_is_not_available_when_not_passed_or_not_issued(): void
    {
        $admin = User::factory()->admin()->create();
        $failed = BeltPromotion::factory()->tidakLulus()->create();
        $passedWithoutNumber = BeltPromotion::factory()->lulus()->create();
        $passedWithoutNumber->forceFill(['certificate_number' => null])->saveQuietly();

        $this->actingAs($admin)
            ->get(route('belt-promotions.certificate', $failed))
            ->assertNotFound();

        $this->actingAs($admin)
            ->get(route('belt-promotions.certificate', $passedWithoutNumber))
            ->assertNotFound();
    }

    public function test_admin_and_operator_can_open_payment_receipt(): void
    {
        $payment = Payment::factory()->paid()->create();

        foreach ([UserRole::Admin, UserRole::Operator] as $role) {
            $user = User::factory()->create(['role' => $role]);

            $this->actingAs($user)
                ->get(route('payments.receipt', $payment))
                ->assertOk()
                ->assertSee($payment->invoice_number);
        }
    }

    public function test_belt_promotions_admin_page_renders_certificate_action(): void
    {
        $admin = User::factory()->admin()->create();
        $promotion = BeltPromotion::factory()->lulus()->create();

        $this->actingAs($admin)
            ->get('/admin/belt-promotions')
            ->assertOk()
            ->assertSee('sertifikat-ukt/'.$promotion->id);
    }
}
