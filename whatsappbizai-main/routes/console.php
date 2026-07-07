<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// Relances factures échues — tous les jours à 09h00
Schedule::command('reminders:send-overdue')->dailyAt('09:00')->withoutOverlapping();

// Rapport quotidien au propriétaire — tous les jours à 08h00
Schedule::command('report:daily')->dailyAt('08:00')->withoutOverlapping();

// Traitement queue (sans worker permanent — compatible hébergement mutualisé)
Schedule::command('queue:work --stop-when-empty --tries=3')
    ->everyMinute()
    ->withoutOverlapping()
    ->runInBackground();
