<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('queue:work --tries=3 --stop-when-empty')
            ->everyMinute()
            ->withoutOverlapping()
            ->runInBackground();

        $schedule->call(function () {
            $jobs = DB::table('failed_jobs')->count();

            if ($jobs >= 1) {
                Artisan::call('queue:retry all');
            }
        })->everyMinute();

        $schedule->command('apps:check_subscription')
            ->weekly()
            ->timezone(config('app.timezone'));
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
