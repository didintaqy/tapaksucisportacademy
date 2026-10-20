<?php

namespace Tests\Feature;

use App\Models\Gallery;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GalleryTest extends TestCase
{
    use RefreshDatabase;

    public function test_gallery_page_shows_published_photos_only(): void
    {
        $published = Gallery::factory()->create(['title' => 'Latihan Rutin Pagi']);
        $hidden = Gallery::factory()->unpublished()->create(['title' => 'Foto Rahasia']);

        $this->get(route('gallery'))
            ->assertOk()
            ->assertSee($published->title)
            ->assertDontSee($hidden->title);
    }

    public function test_gallery_page_renders_when_empty(): void
    {
        $this->get(route('gallery'))
            ->assertOk()
            ->assertSee('Belum ada foto kegiatan');
    }

    public function test_image_url_resolves_theme_and_storage_paths(): void
    {
        $theme = Gallery::factory()->make(['image' => 'images/gallery-1.jpg']);
        $storage = Gallery::factory()->make(['image' => 'gallery/photo.jpg']);

        $this->assertStringContainsString('images/gallery-1.jpg', $theme->image_url);
        $this->assertStringContainsString('storage/gallery/photo.jpg', $storage->image_url);
    }

    public function test_admin_gallery_resource_pages_load(): void
    {
        $this->actingAs(User::factory()->admin()->create());

        $gallery = Gallery::factory()->create();

        $this->get('/admin/galleries')->assertOk();
        $this->get('/admin/galleries/create')->assertOk();
        $this->get("/admin/galleries/{$gallery->id}/edit")->assertOk();
    }
}
