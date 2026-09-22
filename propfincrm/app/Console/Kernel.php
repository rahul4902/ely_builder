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
        Commands\Inspire::class,
        Commands\Test::class,
        Commands\SyncCompanyLeadIntegrations::class,
        Commands\RunScheduledLeadAssignments::class,
        Commands\PrepareCentralDatabase::class,
        Commands\AdoptLegacyTenant::class,
        Commands\RepairTenantDatabase::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('inspire')
                 ->hourly();
        $schedule->command('leads:sync-integrations')->everyFifteenMinutes()->withoutOverlapping();
        $schedule->command('leads:auto-assign')->everyMinute()->withoutOverlapping();
    }
}
