<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    /**
     * Tampilkan daftar artikel yang sudah terbit, opsional difilter kategori/tag.
     */
    public function index(Request $request)
    {
        $activeCategory = filled($request->query('category'))
            ? Category::where('slug', $request->query('category'))->first()
            : null;

        $activeTag = filled($request->query('tag'))
            ? Tag::where('slug', $request->query('tag'))->first()
            : null;

        $posts = Post::query()
            ->published()
            ->ordered()
            ->when($activeCategory, fn ($query) => $query->where('category_id', $activeCategory->id))
            ->when($activeTag, fn ($query) => $query->whereHas('tags', fn ($q) => $q->where('tags.id', $activeTag->id)))
            ->with(['categoryRelation', 'tags'])
            ->paginate(6)
            ->withQueryString();

        $categories = Category::query()
            ->whereHas('posts', fn ($query) => $query->published())
            ->ordered()
            ->get();

        $page = 'Blog';
        $breadcrumb = 'Blog';

        return view('frontend.blog', compact('posts', 'categories', 'activeCategory', 'activeTag', 'page', 'breadcrumb'));
    }

    /**
     * Tampilkan detail satu artikel.
     */
    public function show(Post $post)
    {
        abort_unless($post->isPublished(), 404);

        $post->increment('views');
        $post->load(['categoryRelation', 'tags']);

        $page = $post->title;

        return view('frontend.blog-single', compact('post', 'page'));
    }
}
