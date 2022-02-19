<?php

namespace App\Initialize;

use App\Member;
use Aweos\Agnoster\Progress\HasProgress;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class InitializeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, HasProgress;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        app(Initializer::class)->run();
    }
}
