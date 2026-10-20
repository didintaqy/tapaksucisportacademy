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

        return view('frontend.schedules', [
            'page' => $page,
            'groups' => $schedules->groupBy('day_name'),
        ]);
    }
}
