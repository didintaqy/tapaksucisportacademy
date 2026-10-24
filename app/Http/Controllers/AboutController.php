<?php

namespace App\Http\Controllers;

use App\Models\Coach;
use App\Models\Faq;
use App\Models\Program;
use App\Models\Student;
use App\Models\Testimonial;
use Illuminate\Http\Request;

class AboutController extends Controller
{
    /**
     * Tampilkan profil akademi dari pengaturan situs dan data publik.
     */
    public function __invoke(Request $request)
    {
        $coaches = Coach::query()
            ->active()
            ->ordered()
            ->get();

        $testimonials = Testimonial::query()
            ->approved()
            ->orderByDesc('is_featured')
            ->latest('published_at')
            ->limit(6)
            ->get();

        $faqs = Faq::query()
            ->published()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->limit(4)
            ->get();

        $stats = [
            'students' => Student::query()->count(),
            'programs' => Program::query()->active()->count(),
            'coaches' => $coaches->count(),
            'testimonials' => Testimonial::query()->approved()->count(),
        ];

        $page = 'Tentang Kami';
        $breadcrumb = 'Tentang Kami';

        return view('frontend.about', compact(
            'coaches',
            'testimonials',
            'faqs',
            'stats',
            'page',
            'breadcrumb',
        ));
    }
}
