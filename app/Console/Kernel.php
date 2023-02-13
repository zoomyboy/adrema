<?php

namespace App\Console;

use App\Initialize\Actions\InitializeAction;
use App\Initialize\InitializeMembers;
use App\Letter\Actions\LetterSendAction;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Laravel\Telescope\Console\PruneCommand;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array<int, class-string>
     */
    protected $commands = [
        LetterSendAction::class,
        InitializeAction::class,
        InitializeMembers::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command(PruneCommand::class, ['--hours' => 168])->daily();     // 168h = 7 Tage
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
