<?php

namespace ICT\Console;

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
        \ICT\Console\Commands\Inspire::class,
        \ICT\Console\Commands\FetchFacebookEvents::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('fetch:facebook-events')
                 ->twiceDaily()
                 ->withoutOverlapping()
                 ->sendOutputTo('storage/logs/fetcher.txt');
    }
}
