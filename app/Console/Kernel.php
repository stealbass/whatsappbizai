<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        // Relances factures échues — tous les jours à 9h00
        $schedule->command('reminders:send-overdue')
                 ->dailyAt('09:00')
                 ->withoutOverlapping();

        // Traitement des jobs en queue (pour hébergement sans worker permanent)
        $schedule->command('queue:work --stop-when-empty --tries=3')
                 ->everyMinute()
                 ->withoutOverlapping()
                 ->runInBackground();
    }

    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');
        require base_path('routes/console.php');
    }
}
