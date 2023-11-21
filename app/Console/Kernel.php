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
        'App\Console\Commands\feedPresupuesto'
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //$schedule->command('update:OP')->everyMinute()->appendOutputTo(storage_path('logs/feedPresupuesto.log'));
        //$schedule->command('feed:presupuesto')->everyMinute()->appendOutputTo(storage_path('logs/feedPresupuesto.log'));
        //$schedule->command('feed:presupuestoIngresos')->everyMinute()->appendOutputTo(storage_path('logs/feedPresupuesto.log'));
        //$schedule->command('make:Balances')->everyMinute()->appendOutputTo(storage_path('logs/feedPresupuesto.log'));
        $schedule->command('validate:saldos')->everyMinute()->appendOutputTo(storage_path('logs/feedPresupuesto.log'));
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
