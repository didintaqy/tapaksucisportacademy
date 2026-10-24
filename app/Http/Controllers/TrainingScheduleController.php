<?php

namespace App\Http\Controllers;

use App\Models\TrainingSchedule;

class TrainingScheduleController extends Controller
{
    public function __invoke()
    {
        $schedules = TrainingSchedule::query()
            ->active()
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        $page = 'Jadwal Latihan';
        $breadcrumb = 'Jadwal Latihan';

        return view('frontend.schedules', [
            'page' => $page,
            'breadcrumb' => $breadcrumb,
            'groups' => $schedules->groupBy('day_name'),
        ]);
    }
}
