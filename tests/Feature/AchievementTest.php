<?php

namespace Tests\Feature;

use App\Enums\CompetitionLevel;
use App\Enums\MedalType;
use App\Enums\UserRole;
use App\Models\Achievement;
use App\Models\Student;
use App\Models\TrainingSchedule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AchievementTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_achievements_page_displays_published_achievements_and_summary(): void
    {
        $student = Student::factory()->create(['full_name' => 'Ahmad Fauzan']);

        Achievement::factory()->create([
            'student_id' => $student->id,
            'competition_name' => 'Kejuaraan Daerah 5 Tapak Suci',
            'level' => CompetitionLevel::Provinsi,
            'medal' => MedalType::Emas,
            'is_published' => true,
        ]);

        Achievement::factory()->create([
            'competition_name' => 'Kejuaraan Internal Rahasia',
            'is_published' => false,
        ]);

        $response = $this->get(route('achievements'));

        $response->assertOk()
            ->assertSee('Kejuaraan Daerah 5 Tapak Suci')
            ->assertSee('Ahmad Fauzan')
            ->assertDontSee('Kejuaraan Internal Rahasia');
    }

    public function test_public_achievements_page_can_filter_by_medal_and_level(): void
    {
        Achievement::factory()->create([
            'competition_name' => 'Turnamen Emas Nasional',
            'level' => CompetitionLevel::Nasional,
            'medal' => MedalType::Emas,
            'is_published' => true,
        ]);

        Achievement::factory()->create([
            'competition_name' => 'Turnamen Perak Kota',
            'level' => CompetitionLevel::Kota,
            'medal' => MedalType::Perak,
            'is_published' => true,
        ]);

        $this->get(route('achievements', ['medal' => 'emas', 'level' => 'nasional']))
            ->assertOk()
            ->assertSee('Turnamen Emas Nasional')
            ->assertDontSee('Turnamen Perak Kota');
    }

    public function test_achievement_policy_scopes_pelatih_to_their_branch_and_prevents_delete(): void
    {
        $admin = User::factory()->admin()->create();
        $operator = User::factory()->create(['role' => UserRole::Operator]);
        $pelatih = User::factory()->create(['role' => UserRole::Pelatih]);

        TrainingSchedule::factory()->create([
            'coach_id' => $pelatih->id,
            'branch' => 'Batam Centre',
        ]);

        $ownBranchStudent = Student::factory()->create(['branch' => 'Batam Centre']);
        $otherBranchStudent = Student::factory()->create(['branch' => 'Batu Aji']);

        $ownAchievement = Achievement::factory()->create(['student_id' => $ownBranchStudent->id]);
        $otherAchievement = Achievement::factory()->create(['student_id' => $otherBranchStudent->id]);

        $this->assertTrue($pelatih->can('view', $ownAchievement));
        $this->assertTrue($pelatih->can('update', $ownAchievement));
        $this->assertFalse($pelatih->can('view', $otherAchievement));
        $this->assertFalse($pelatih->can('delete', $ownAchievement));

        $this->assertTrue($operator->can('delete', $ownAchievement));
        $this->assertTrue($admin->can('delete', $ownAchievement));
    }

    public function test_admin_can_access_achievement_filament_resource(): void
    {
        $admin = User::factory()->admin()->create();
        Achievement::factory()->create(['competition_name' => 'King of Silat Batam']);

        $this->actingAs($admin)
            ->get('/admin/achievements')
            ->assertOk()
            ->assertSee('King of Silat Batam');
    }
}
