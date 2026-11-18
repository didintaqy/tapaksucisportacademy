<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CalendarController extends Controller
{
    public function __invoke(Request $request): View
    {
        $timezone = config('app.display_timezone');

        $month = $request->integer('month', now($timezone)->month);
        $year = $request->integer('year', now($timezone)->year);

        // Validasi bulan dan tahun
        $month = max(1, min(12, $month));
        $year = max(now($timezone)->year - 2, min(now($timezone)->year + 2, $year));

        // Batas bulan dihitung di zona tampilan, lalu dikonversi ke UTC
        // karena `starts_at` disimpan dalam UTC.
        $startOfMonth = now($timezone)->setDate($year, $month, 1)->startOfDay();
        $endOfMonth = now($timezone)->setDate($year, $month, 1)->endOfMonth()->endOfDay();

        $events = Event::public()
            ->whereBetween('starts_at', [
                $startOfMonth->copy()->utc(),
                $endOfMonth->copy()->utc(),
            ])
            ->orderBy('starts_at')
            ->get()
            ->groupBy(fn ($e) => $e->starts_at->copy()->tz($timezone)->day);

        $daysInMonth = $startOfMonth->daysInMonth;
        $firstDayOfWeek = (int) $startOfMonth->dayOfWeek; // 0 = Minggu

        return view('frontend.calendar', [
            'month' => $month,
            'year' => $year,
            'daysInMonth' => $daysInMonth,
            'firstDayOfWeek' => $firstDayOfWeek,
            'eventsByDay' => $events,
            'today' => now($timezone),
            'monthName' => $startOfMonth->locale('id')->isoFormat('MMMM'),
        ]);
    }
}
