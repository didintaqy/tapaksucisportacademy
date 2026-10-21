<?php

namespace App\Http\Controllers;

use App\Models\Program;
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

        $page = 'Program';
        $seoTitle = 'Program Latihan — Tapak Suci Sport Academy';
        $seoDescription = 'Program latihan pencak silat Tapak Suci untuk anak, remaja, dewasa, hingga jalur prestasi.';

        return view('frontend.program', compact(
            'programs',
            'branches',
            'activeBranch',
            'page',
            'seoTitle',
            'seoDescription',
        ));
    }

    public function show(Program $program)
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

        $page = $program->name;
        $seoTitle = $program->meta_title ?: $program->name.' — Tapak Suci Sport Academy';
        $seoDescription = $program->meta_description ?: $program->short_description;
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
