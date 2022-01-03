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
        app(Initializer::class)->run($this->user);
    }
}
