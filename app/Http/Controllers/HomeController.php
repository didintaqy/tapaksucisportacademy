<?php

namespace App\Http\Controllers;

use App\Models\Achievement;
use App\Models\Coach;
use App\Models\Faq;
use App\Models\Post;
use App\Models\Program;
use App\Models\Student;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    /**
     * Tampilkan beranda dengan seluruh konten dari database dan pengaturan situs.
     */
    public function __invoke(Request $request)
    {
        $data = Cache::remember('homepage.data', 60 * 60, function () {
            $programs = Program::query()
                ->active()
                ->ordered()
                ->get();

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
                ->limit(6)
                ->get();

            $posts = Post::query()
                ->published()
                ->ordered()
                ->with('categoryRelation')
                ->limit(3)
                ->get();

            $achievements = Achievement::query()
                ->published()
                ->with('student')
                ->orderByDesc('event_date')
                ->limit(6)
                ->get();

            $stats = [
                'students' => Student::query()->count(),
                'programs' => $programs->count(),
                'coaches' => $coaches->count(),
                'testimonials' => $testimonials->count(),
            ];

            return compact('programs', 'coaches', 'testimonials', 'faqs', 'posts', 'achievements', 'stats');
        });

        return view('frontend.home', $data);
    }
}
