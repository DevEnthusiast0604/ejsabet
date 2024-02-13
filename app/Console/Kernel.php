<?php

namespace App\Console;

use App\Models\Setting;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\SiteDisable::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $disableTimeline = Setting::get('site_disable_time');
        if ($disableTimeline) $disableTimeline = json_decode($disableTimeline, true);
        foreach ($disableTimeline as $item) {
            $schedule->command('site:disable inactive')->dailyAt($item['start']);
            $schedule->command('site:disable active')->dailyAt($item['end']);
        }
        $schedule->command('app:news-load')->dailyAt('08:00');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
