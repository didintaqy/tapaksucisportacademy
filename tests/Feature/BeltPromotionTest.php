<?php

namespace Tests\Feature;

use App\Enums\BeltRank;
use App\Enums\PromotionResult;
use App\Models\BeltPromotion;
use App\Models\Student;
use App\Models\User;
use Database\Seeders\StudentsTableSeeder;
use Database\Seeders\UsersTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BeltPromotionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(UsersTableSeeder::class);
        $this->seed(StudentsTableSeeder::class);
    }

    public function test_admin_can_view_belt_promotion_list(): void
    {
        $admin = User::where('role', 'admin')->first();
        $this->actingAs($admin);

        $this->get(route('filament.admin.resources.belt-promotions.index'))
            ->assertOk()
            ->assertSee('Sabuk / UKT');
    }

    public function test_belt_promotion_creation(): void
    {
        $student = Student::first();
        $user = User::where('role', 'pelatih')->first();

        $promotion = BeltPromotion::create([
            'student_id' => $student->id,
            'examined_by' => $user->id,
            'exam_date' => now()->format('Y-m-d'),
            'current_belt' => 'putih',
            'target_belt' => 'kuning',
            'result' => PromotionResult::Lulus,
            'notes' => 'Test promotion',
            'examiner_notes' => 'Good performance',
            'certificate_issued_at' => now()->format('Y-m-d'),
            'certificate_number' => 'UKT-2026-0001',
        ]);

        $this->assertDatabaseHas('belt_promotions', [
            'id' => $promotion->id,
            'student_id' => $student->id,
            'result' => 'lulus',
        ]);
    }

    public function test_belt_promotion_accessors(): void
    {
        $promotion = BeltPromotion::factory()->make([
            'current_belt' => 'putih',
            'target_belt' => 'kuning',
            'result' => PromotionResult::Lulus,
        ]);

        $this->assertEquals('Putih', $promotion->current_belt_label);
        $this->assertEquals('Kuning', $promotion->target_belt_label);
        $this->assertEquals('Lulus', $promotion->result_label);
        $this->assertEquals('success', $promotion->result_color);
        $this->assertEquals('heroicon-o-check-circle', $promotion->result_icon);

        $promotion->result = PromotionResult::TidakLulus;
        $this->assertEquals('Tidak Lulus', $promotion->result_label);
        $this->assertEquals('danger', $promotion->result_color);
        $this->assertEquals('heroicon-o-x-circle', $promotion->result_icon);

        $promotion->result = PromotionResult::Ditunda;
        $this->assertEquals('Ditunda', $promotion->result_label);
        $this->assertEquals('warning', $promotion->result_color);
        $this->assertEquals('heroicon-o-clock', $promotion->result_icon);
    }

    public function test_belt_rank_enum(): void
    {
        $this->assertEquals('Putih', BeltRank::Putih->label());
        $this->assertEquals('Kuning', BeltRank::Kuning->label());
        $this->assertEquals('Hitam 1 Dan', BeltRank::Hitam1->label());
        $this->assertEquals('gray', BeltRank::Putih->color());
        $this->assertEquals('warning', BeltRank::Kuning->color());
        $this->assertEquals('dark', BeltRank::Hitam1->color());

        $this->assertEquals(BeltRank::Kuning, BeltRank::Putih->next());
        $this->assertEquals(BeltRank::Hijau, BeltRank::Kuning->next());
        $this->assertNull(BeltRank::Hitam5->next());

        $this->assertFalse(BeltRank::Putih->isBlackBelt());
        $this->assertTrue(BeltRank::Hitam1->isBlackBelt());
    }

    public function test_promotion_result_enum(): void
    {
        $this->assertEquals('Lulus', PromotionResult::Lulus->label());
        $this->assertEquals('Tidak Lulus', PromotionResult::TidakLulus->label());
        $this->assertEquals('Ditunda', PromotionResult::Ditunda->label());
        $this->assertEquals('success', PromotionResult::Lulus->color());
        $this->assertEquals('danger', PromotionResult::TidakLulus->color());
        $this->assertEquals('warning', PromotionResult::Ditunda->color());
    }
}
