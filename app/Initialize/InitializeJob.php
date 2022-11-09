<?php

namespace App\Initialize;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class InitializeJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct()
    {
        $this->onQueue('long');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        app(Initializer::class)->run();
    }

    public function failed(Throwable $e): void
    {
        app(Initializer::class)->restore();
    }
}
