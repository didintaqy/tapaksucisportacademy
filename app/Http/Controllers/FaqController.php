<?php

namespace App\Http\Controllers;

use App\Models\Faq;

class FaqController extends Controller
{
    public function __invoke()
    {
        $faqs = Faq::query()
            ->published()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $page = 'FAQ';

        return view('frontend.faqs', compact('faqs', 'page'));
    }
}
