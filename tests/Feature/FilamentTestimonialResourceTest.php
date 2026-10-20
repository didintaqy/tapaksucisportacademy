<?php

namespace Tests\Feature;

use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FilamentTestimonialResourceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->admin()->create());
    }

    public function test_testimonial_resource_pages_load(): void
    {
        $testimonial = Testimonial::factory()->create();

        $this->get('/admin/testimonials')->assertOk();
        $this->get('/admin/testimonials/create')->assertOk();
        $this->get("/admin/testimonials/{$testimonial->id}/edit")->assertOk();
    }
}
