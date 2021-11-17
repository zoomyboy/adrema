<?php

namespace App\Initialize;

use App\Member;
use Aweos\Agnoster\Progress\HasProgress;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Zoomyboy\LaravelNami\NamiUser;

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
        InitializeMembers::class,
    ];

    public function __construct(NamiUser $user)
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
        $api = $this->user->api();
        $bar = $this->createProgressBar('Initialisiere');

        foreach (static::$initializers as $initializer) {
            (new $initializer($bar, $api))->handle();
        }

        $bar->finish('Initialisierung abgeschlossen');
    }
}
