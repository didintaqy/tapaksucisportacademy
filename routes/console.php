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
    ->timezone(config('app.display_timezone'))
    ->withoutOverlapping()
    ->onOneServer();

// Bersihkan failed job yang lebih tua dari 7 hari setiap dini hari.
Schedule::command('queue:prune-failed --hours=168')
    ->dailyAt('03:00')
    ->timezone(config('app.display_timezone'))
    ->withoutOverlapping()
    ->onOneServer();

// Hapus gambar Pengaturan Situs yang tidak lagi dipakai. Jaring pengaman
// bila berkas yatim muncul dari seeder atau perubahan data manual.
Schedule::command('site-settings:prune-media')
    ->dailyAt('03:15')
    ->timezone(config('app.display_timezone'))
    ->withoutOverlapping()
    ->onOneServer();

// Backup database setiap hari pukul 02:00. Kegagalan dicatat ke log agar
// terlihat di pemeriksaan harian (LOG_LEVEL=warning).
Schedule::command('db:backup')
    ->dailyAt('02:00')
    ->timezone(config('app.display_timezone'))
    ->withoutOverlapping()
    ->onOneServer()
    ->onFailure(fn (): mixed => logger()->error('Backup database harian gagal dijalankan.'));

// Alert bila antrean menumpuk di atas 100 job (worker mungkin mati).
// Perintah ini mengirim notifikasi ke MAIL_ALERT_ADDRESS.
Schedule::command('queue:monitor default:100')
    ->everyFiveMinutes()
    ->timezone(config('app.display_timezone'))
    ->withoutOverlapping()
    ->onOneServer();
