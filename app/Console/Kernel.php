<?php

namespace App\Console;

use App\Console\Commands\ImportFromSQLSERVER;
use App\Console\Commands\RunBatchImport;
use App\Console\Commands\UpdateStatusEmptyInfo;
use App\Console\Commands\ConvertDataByExcels;
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
        UpdateStatusEmptyInfo::class,
        ConvertDataByExcels::class,
        RunBatchImport::class,
        ImportFromSQLSERVER::class,
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
        $schedule->command('UpdateStatusEmptyInfo')->daily();
        /*$schedule->command('ImportFromSQLSERVER')
            ->cron(config("params.runImportFromSqlServer.cron"))->skip(function () {
            return true;
        });*/
        $schedule->command('ImportFromSQLSERVER')
            ->everyMinute();
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
