<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CalendarController extends Controller
{
    public function __invoke(Request $request): View
    {
        $month = $request->integer('month', now()->month);
        $year = $request->integer('year', now()->year);

        // Validasi bulan dan tahun
        $month = max(1, min(12, $month));
        $year = max(now()->year - 2, min(now()->year + 2, $year));

        $startOfMonth = now()->setDate($year, $month, 1)->startOfDay();
        $endOfMonth = now()->setDate($year, $month, 1)->endOfMonth()->endOfDay();

        $events = Event::public()
            ->whereBetween('starts_at', [$startOfMonth, $endOfMonth])
            ->orderBy('starts_at')
            ->get()
            ->groupBy(fn ($e) => $e->starts_at->day);

        $daysInMonth = $startOfMonth->daysInMonth;
        $firstDayOfWeek = (int) $startOfMonth->dayOfWeek; // 0 = Minggu

        return view('frontend.calendar', [
            'month' => $month,
            'year' => $year,
            'daysInMonth' => $daysInMonth,
            'firstDayOfWeek' => $firstDayOfWeek,
            'eventsByDay' => $events,
            'today' => now(),
            'monthName' => $startOfMonth->locale('id')->isoFormat('MMMM'),
        ]);
    }
}
