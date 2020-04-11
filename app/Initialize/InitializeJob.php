<?php

namespace App\Initialize;

use App\User;
use App\Member;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Aweos\Agnoster\Progress\HasProgress;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class InitializeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, HasProgress;

    public $user;

    public static $initializers = [
        InitializeNationalities::class,
        InitializeFees::class,
        InitializeConfessions::class,
        InitializeCountries::class,
        InitializeGenders::class,
        InitializeRegions::class,
        InitializeActivities::class,
    ];

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $api = $this->user->getNamiApi();
        $bar = $this->createProgressBar('Initialisiere');

        foreach (static::$initializers as $initializer) {
            (new $initializer($bar, $api))->handle();
        }

        $bar->finish('Initialisierung abgeschlossen');
    }
}
