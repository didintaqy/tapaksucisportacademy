<?php

namespace Tests\Feature;

use App\Models\Faq;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FilamentFaqResourceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->admin()->create());
    }

    public function test_faq_resource_pages_load(): void
    {
        $faq = Faq::factory()->create();

        $this->get('/admin/faqs')->assertOk();
        $this->get('/admin/faqs/create')->assertOk();
        $this->get("/admin/faqs/{$faq->id}/edit")->assertOk();
    }
}
