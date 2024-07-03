<?php

namespace App\Console;

use App\Actions\DbMaintainAction;
use App\Initialize\InitializeMembers;
use App\Invoice\Actions\InvoiceSendAction;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array<int, class-string>
     */
    protected $commands = [
        InvoiceSendAction::class,
        InitializeMembers::class,
        DbMaintainAction::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command(DbMaintainAction::class)->daily();
        $schedule->command(InitializeMembers::class)->dailyAt('03:00');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
