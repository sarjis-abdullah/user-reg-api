<?php

namespace App\Console;

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
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('backup:run --only-db')->dailyAt('01:00');
        $schedule->command('backup:clean')->dailyAt('02:00');

        // product expired scheduled job
        $schedule->command('pos:product-date-expired-notification --day=7')
            ->dailyAt('10:00')
            ->name("Scheduled Job - Product Date Expiration!")
            //->emailOutputTo('dev@reformedtech.org')
            ->emailOutputOnFailure('dev@reformedtech.org');

        // product expired list scheduled job
        $schedule->command('pos:product-date-expired-notification --day=14')
            ->dailyAt('12:00')
            ->name("Scheduled Job - Product Date Expiration!")
            //->emailOutputTo('dev@reformedtech.org')
            ->emailOutputOnFailure('dev@reformedtech.org');

        //forcefully user session delete
        $schedule->command('force:logout')
            ->dailyAt('02:00');

        //Store Daily Summary report
        $schedule->command('store:daily_summary_report_history')
            ->dailyAt('00:00');
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
