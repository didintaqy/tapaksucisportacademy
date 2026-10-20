<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Faq;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FaqTest extends TestCase
{
    use RefreshDatabase;

    public function test_faq_page_shows_only_published_items(): void
    {
        Faq::factory()->create([
            'category' => 'Latihan',
            'question' => 'Apakah pemula boleh ikut?',
            'answer' => 'Tentu saja boleh.',
        ]);

        Faq::factory()->unpublished()->create([
            'question' => 'Pertanyaan yang disembunyikan?',
        ]);

        $this->get(route('faqs'))
            ->assertOk()
            ->assertSee('Latihan')
            ->assertSee('Apakah pemula boleh ikut?')
            ->assertSee('Tentu saja boleh.')
            ->assertDontSee('Pertanyaan yang disembunyikan?');
    }

    public function test_faq_page_groups_items_by_category(): void
    {
        Faq::factory()->create(['category' => 'Pendaftaran', 'question' => 'Pertanyaan daftar?', 'sort_order' => 1]);
        Faq::factory()->create(['category' => 'Latihan', 'question' => 'Pertanyaan latihan?', 'sort_order' => 2]);

        $this->get(route('faqs'))
            ->assertOk()
            ->assertSeeInOrder(['Pendaftaran', 'Pertanyaan daftar?', 'Latihan', 'Pertanyaan latihan?']);
    }

    public function test_faq_permissions(): void
    {
        $admin = User::factory()->admin()->create();
        $operator = User::factory()->create(['role' => UserRole::Operator]);
        $coach = User::factory()->create(['role' => UserRole::Pelatih]);
        $faq = Faq::factory()->create();

        $this->assertTrue($admin->can('viewAny', Faq::class));
        $this->assertTrue($operator->can('update', $faq));
        $this->assertFalse($coach->can('viewAny', Faq::class));
        $this->assertFalse($operator->can('delete', $faq));
        $this->assertTrue($admin->can('delete', $faq));
    }
}
