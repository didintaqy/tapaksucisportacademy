<?php

namespace App\Http\Controllers;

use App\Enums\CompetitionLevel;
use App\Enums\MedalType;
use App\Models\Achievement;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AchievementController extends Controller
{
    public function __invoke(Request $request): View
    {
        $level = $request->query('level');
        $medal = $request->query('medal');
        $year = $request->query('year');
        $search = trim((string) $request->query('q', ''));

        $baseQuery = Achievement::published();

        $summary = [
            'total' => (clone $baseQuery)->count(),
            'emas' => (clone $baseQuery)->where('medal', MedalType::Emas->value)->count(),
            'perak' => (clone $baseQuery)->where('medal', MedalType::Perak->value)->count(),
            'perunggu' => (clone $baseQuery)->where('medal', MedalType::Perunggu->value)->count(),
            'juara_umum' => (clone $baseQuery)->where('medal', MedalType::JuaraUmum->value)->count(),
        ];

        $years = Achievement::published()
            ->whereNotNull('event_date')
            ->get(['event_date'])
            ->map(fn (Achievement $a) => $a->event_date?->format('Y'))
            ->filter()
            ->unique()
            ->sortDesc()
            ->values();

        $query = Achievement::published()
            ->with('student')
            ->when(
                filled($level) && CompetitionLevel::tryFrom((string) $level),
                fn ($q) => $q->where('level', $level),
            )
            ->when(
                filled($medal) && MedalType::tryFrom((string) $medal),
                fn ($q) => $q->where('medal', $medal),
            )
            ->when(
                filled($year) && ctype_digit((string) $year),
                fn ($q) => $q->whereYear('event_date', (int) $year),
            )
            ->when(
                $search !== '',
                function ($q) use ($search): void {
                    $q->where(function ($sub) use ($search): void {
                        $sub->where('competition_name', 'like', "%{$search}%")
                            ->orWhere('category', 'like', "%{$search}%")
                            ->orWhere('weight_class', 'like', "%{$search}%")
                            ->orWhere('location', 'like', "%{$search}%")
                            ->orWhereHas('student', fn ($sq) => $sq->where('full_name', 'like', "%{$search}%"));
                    });
                },
            )
            ->orderByDesc('event_date')
            ->orderByDesc('id');

        $achievements = $query->paginate(12)->withQueryString();

        return view('frontend.achievements', [
            'achievements' => $achievements,
            'summary' => $summary,
            'years' => $years,
            'levels' => CompetitionLevel::options(),
            'medals' => MedalType::options(),
            'selectedLevel' => $level,
            'selectedMedal' => $medal,
            'selectedYear' => $year,
            'search' => $search,
            'seo' => [
                'title' => 'Prestasi & Kejuaraan Atlet',
                'description' => 'Rekam jejak prestasi, perolehan medali emas, perak, perunggu, dan gelar juara umum atlet Tapak Suci Sport Academy.',
            ],
        ]);
    }
}
