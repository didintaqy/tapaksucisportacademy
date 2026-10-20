<?php

namespace Tests\Feature;

use App\Enums\TestimonialStatus;
use App\Enums\UserRole;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TestimonialTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_submission_is_stored_as_pending(): void
    {
        $response = $this->post(route('testimonials.store'), [
            'name' => 'Rina',
            'relation' => 'Orang Tua Siswa',
            'rating' => 5,
            'content' => 'Pengalaman berlatih sangat baik.',
        ]);

        $response->assertRedirect(route('testimonials'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('testimonials', [
            'name' => 'Rina',
            'status' => TestimonialStatus::Pending->value,
        ]);
    }

    public function test_public_submission_requires_valid_data(): void
    {
        $this->post(route('testimonials.store'), [
            'name' => '',
            'rating' => 9,
            'content' => '',
        ])->assertSessionHasErrors(['name', 'rating', 'content']);
    }

    public function test_honeypot_blocks_spam_submissions(): void
    {
        $this->post(route('testimonials.store'), [
            'name' => 'Spam Bot',
            'rating' => 5,
            'content' => 'Promo murah',
            'website' => 'https://spam.example',
        ])->assertSessionHasErrors('website');

        $this->assertDatabaseCount('testimonials', 0);
    }

    public function test_only_approved_testimonials_are_shown(): void
    {
        Testimonial::factory()->approved()->create([
            'name' => 'Siti',
            'content' => 'Testimoni yang sudah disetujui.',
        ]);

        Testimonial::factory()->create([
            'name' => 'Budi',
            'content' => 'Testimoni yang masih menunggu.',
        ]);

        $this->get(route('testimonials'))
            ->assertOk()
            ->assertSee('Siti')
            ->assertSee('Testimoni yang sudah disetujui.')
            ->assertDontSee('Testimoni yang masih menunggu.');
    }

    public function test_approving_testimonial_sets_published_date(): void
    {
        $testimonial = Testimonial::factory()->create();

        $testimonial->approve();

        $this->assertSame(TestimonialStatus::Approved, $testimonial->fresh()->status);
        $this->assertNotNull($testimonial->fresh()->published_at);
    }

    public function test_testimonial_permissions(): void
    {
        $admin = User::factory()->admin()->create();
        $operator = User::factory()->create(['role' => UserRole::Operator]);
        $coach = User::factory()->create(['role' => UserRole::Pelatih]);
        $testimonial = Testimonial::factory()->create();

        $this->assertTrue($admin->can('viewAny', Testimonial::class));
        $this->assertTrue($operator->can('viewAny', Testimonial::class));
        $this->assertFalse($coach->can('viewAny', Testimonial::class));

        $this->assertTrue($admin->can('delete', $testimonial));
        $this->assertFalse($operator->can('delete', $testimonial));
    }
}
