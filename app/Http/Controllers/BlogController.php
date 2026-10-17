<?php

namespace App\Http\Controllers;

use App\Models\Post;

class BlogController extends Controller
{
    /**
     * Tampilkan daftar artikel yang sudah terbit.
     */
    public function index()
    {
        $posts = Post::query()
            ->published()
            ->ordered()
            ->paginate(6);

        $page = 'Blog';
        $breadcrumb = 'Blog';

        return view('frontend.blog', compact('posts', 'page', 'breadcrumb'));
    }

    /**
     * Tampilkan detail satu artikel.
     */
    public function show(Post $post)
    {
        abort_unless($post->isPublished(), 404);

        $post->increment('views');

        $page = $post->title;

        return view('frontend.blog-single', compact('post', 'page'));
    }
}
