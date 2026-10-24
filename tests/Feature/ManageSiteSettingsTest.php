<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Filament\Pages\ManageSiteSettings;
use App\Filament\Resources\Coaches\CoachResource;
use App\Models\Coach;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ManageSiteSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_and_operator_can_open_site_settings(): void
    {
        foreach ([UserRole::Admin, UserRole::Operator] as $role) {
            $user = User::factory()->create(['role' => $role]);

            $this->actingAs($user)
                ->get(ManageSiteSettings::getUrl())
                ->assertOk()
                ->assertSee('Pengaturan Situs')
                ->assertSee('Simpan Pengaturan');
        }
    }

    public function test_coach_cannot_open_site_settings(): void
    {
        $coach = User::factory()->create(['role' => UserRole::Pelatih]);

        $this->actingAs($coach)
            ->get(ManageSiteSettings::getUrl())
            ->assertForbidden();
    }

    public function test_admin_can_save_site_settings_from_the_page(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin);

        Livewire::test(ManageSiteSettings::class)
            ->assertSet('data.identity.name', 'Tapak Suci Sport Academy')
            ->set('data.identity.name', 'Akademi Baru')
            ->set('data.contact.whatsapp', '081234567890')
            ->call('save')
            ->assertHasNoErrors()
            ->assertNotified('Pengaturan situs disimpan');

        $data = SiteSetting::query()->find(1)?->data;

        $this->assertSame('Akademi Baru', $data['identity']['name'] ?? null);
        $this->assertSame('081234567890', $data['contact']['whatsapp'] ?? null);
    }

    public function test_admin_and_operator_can_manage_coaches(): void
    {
        $coach = Coach::factory()->create(['name' => 'Pelatih Uji']);

        foreach ([UserRole::Admin, UserRole::Operator] as $role) {
            $user = User::factory()->create(['role' => $role]);

            $this->actingAs($user)
                ->get(CoachResource::getUrl('index'))
                ->assertOk()
                ->assertSee('Pelatih Uji');
        }
    }

    public function test_pelatih_role_cannot_manage_coaches(): void
    {
        $user = User::factory()->create(['role' => UserRole::Pelatih]);

        $this->actingAs($user)
            ->get(CoachResource::getUrl('index'))
            ->assertForbidden();
    }

    public function test_only_admin_can_delete_a_coach(): void
    {
        $coach = Coach::factory()->create();
        $operator = User::factory()->create(['role' => UserRole::Operator]);
        $admin = User::factory()->admin()->create();

        $this->assertFalse($operator->can('delete', $coach));
        $this->assertTrue($admin->can('delete', $coach));
    }
}
