<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use App\Models\Program;
use App\Models\Testimonial;
use App\Services\SiteSettings;
use Illuminate\Http\Request;

class ProgramController extends Controller
{
    public function index(Request $request)
    {
        $activeBranch = filled($request->query('branch')) ? (string) $request->query('branch') : null;

        $programs = Program::query()
            ->active()
            ->ordered()
            ->forBranch($activeBranch)
            ->withCount('students')
            ->get();

        $branches = Program::query()
            ->active()
            ->pluck('branches')
            ->flatten()
            ->filter()
            ->unique()
            ->sort()
            ->values();

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

        $page = 'Program Latihan';
        $breadcrumb = 'Program';

        return view('frontend.program', compact(
            'programs',
            'branches',
            'activeBranch',
            'testimonials',
            'faqs',
            'page',
            'breadcrumb',
        ));
    }

    public function show(Program $program, SiteSettings $settings)
    {
        abort_unless($program->is_active, 404);

        $program->load([
            'schedules' => fn ($query) => $query->active()->orderBy('day_of_week')->orderBy('start_time'),
            'schedules.coach',
        ]);

        $otherPrograms = Program::query()
            ->active()
            ->whereKeyNot($program->getKey())
            ->ordered()
            ->limit(6)
            ->get();

        $siteName = $settings->get('identity.name', 'Tapak Suci Sport Academy');

        $page = $program->name;
        $seoTitle = $program->meta_title ?: $program->name.' — '.$siteName;
        $seoDescription = $program->meta_description
            ?: $program->short_description
            ?: $settings->get('seo.description');
        $seoImage = $program->cover_url ?: null;

        return view('frontend.program-single', compact(
            'program',
            'otherPrograms',
            'page',
            'seoTitle',
            'seoDescription',
            'seoImage',
        ));
    }
}
