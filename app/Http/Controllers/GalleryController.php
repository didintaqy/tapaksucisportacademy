<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $galleries = Gallery::query()
            ->published()
            ->ordered()
            ->get();

        $page = 'Galeri';
        $breadcrumb = 'Galeri';

        return view('frontend.gallery', compact('galleries', 'page', 'breadcrumb'));
    }
}
