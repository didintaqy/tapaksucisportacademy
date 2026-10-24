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

// Bersihkan failed job yang lebih tua dari 7 hari setiap dini hari.
Schedule::command('queue:prune-failed --hours=168')
    ->dailyAt('03:00')
    ->withoutOverlapping()
    ->onOneServer();

// Backup database setiap hari pukul 02:00.
Schedule::command('db:backup')
    ->dailyAt('02:00')
    ->withoutOverlapping()
    ->onOneServer();
