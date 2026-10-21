<?php

namespace Tests\Feature;

use App\Enums\PostStatus;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
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

    public function test_blog_filters_posts_by_category(): void
    {
        $category = Category::factory()->create(['name' => 'Latihan', 'slug' => 'latihan']);
        $other = Category::factory()->create(['name' => 'Tips', 'slug' => 'tips']);

        $match = Post::factory()->create(['title' => 'Artikel Latihan', 'category_id' => $category->id]);
        $miss = Post::factory()->create(['title' => 'Artikel Tips', 'category_id' => $other->id]);

        $this->get(route('blog', ['category' => 'latihan']))
            ->assertOk()
            ->assertSee($match->title)
            ->assertDontSee($miss->title);
    }

    public function test_blog_filters_posts_by_tag(): void
    {
        $tag = Tag::factory()->create(['name' => 'pemula', 'slug' => 'pemula']);

        $match = Post::factory()->create(['title' => 'Artikel Pemula']);
        $match->tags()->attach($tag->id);

        $miss = Post::factory()->create(['title' => 'Artikel Lanjutan']);

        $this->get(route('blog', ['tag' => 'pemula']))
            ->assertOk()
            ->assertSee($match->title)
            ->assertDontSee($miss->title);
    }

    public function test_blog_show_displays_category_and_tags(): void
    {
        $category = Category::factory()->create(['name' => 'Kejuaraan', 'slug' => 'kejuaraan']);
        $tag = Tag::factory()->create(['name' => 'kompetisi', 'slug' => 'kompetisi']);

        $post = Post::factory()->create([
            'title' => 'Artikel Kejuaraan',
            'slug' => 'artikel-kejuaraan',
            'category_id' => $category->id,
        ]);
        $post->tags()->attach($tag->id);

        $this->get(route('blog.show', $post->slug))
            ->assertOk()
            ->assertSee('Kejuaraan')
            ->assertSee('kompetisi');
    }
}
