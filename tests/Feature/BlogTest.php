<?php

namespace Tests\Feature;

use App\Enums\PostStatus;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlogTest extends TestCase
{
    use RefreshDatabase;

    public function test_blog_index_lists_only_published_posts(): void
    {
        $published = Post::factory()->create(['title' => 'Artikel Terbit']);
        $draft = Post::factory()->draft()->create(['title' => 'Artikel Draf']);

        $this->get(route('blog'))
            ->assertOk()
            ->assertSee($published->title)
            ->assertDontSee($draft->title);
    }

    public function test_blog_index_renders_when_empty(): void
    {
        $this->get(route('blog'))
            ->assertOk()
            ->assertSee('Belum ada artikel');
    }

    public function test_blog_show_displays_post_and_increments_views(): void
    {
        $post = Post::factory()->create([
            'slug' => 'artikel-pertama',
            'title' => 'Artikel Pertama',
            'content' => '<p>Isi artikel untuk pengujian.</p>',
            'views' => 5,
        ]);

        $this->get(route('blog.show', $post->slug))
            ->assertOk()
            ->assertSee('Artikel Pertama')
            ->assertSee('Isi artikel untuk pengujian.', false);

        $this->assertSame(6, $post->fresh()->views);
    }

    public function test_blog_show_returns_404_for_draft(): void
    {
        $draft = Post::factory()->draft()->create(['slug' => 'draf-artikel']);

        $this->get(route('blog.show', $draft->slug))->assertNotFound();
    }

    public function test_admin_post_resource_pages_load(): void
    {
        $this->actingAs(User::factory()->admin()->create());

        $post = Post::factory()->create();

        $this->get('/admin/posts')->assertOk();
        $this->get('/admin/posts/create')->assertOk();
        $this->get("/admin/posts/{$post->id}/edit")->assertOk();
    }

    public function test_slug_is_generated_from_title(): void
    {
        $post = Post::factory()->create([
            'title' => 'Latihan Rutin Tapak Suci',
            'slug' => null,
        ]);

        $this->assertSame('latihan-rutin-tapak-suci', $post->slug);
    }

    public function test_published_scope_excludes_future_dated_posts(): void
    {
        $post = Post::factory()->create([
            'status' => PostStatus::Published,
            'published_at' => now()->addWeek(),
        ]);

        $this->assertFalse(Post::query()->published()->whereKey($post->id)->exists());
    }
}
