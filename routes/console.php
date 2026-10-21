<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Kirim pengingat tagihan setiap hari pukul 08:00.
Schedule::command('payments:send-reminders --days=3')
    ->dailyAt('08:00')
    ->withoutOverlapping()
    ->onOneServer();
