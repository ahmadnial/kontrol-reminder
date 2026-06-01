<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Eksekusi Command WA otomatis setiap jam 08:00 pagi
        $schedule->command('wa:reminder-kontrol')->dailyAt('08:00');
        // $schedule->command('wa:reminder-kontrol')->everyFiveMinutes(); // Untuk testing, jalankan setiap 5 menit
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }

    
}
