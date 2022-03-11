<?php

namespace App\Console\Commands;

use App\Initialize\Initializer;
use Illuminate\Console\Command;
use Zoomyboy\LaravelNami\NamiException;

class NamiInitializeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nami:initialize {--mglnr=} {--password=} {--group_id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initializes nami';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            app(Initializer::class)->run();
        } catch (NamiException $e) {
            $e->outputToConsole($this);

            return 1;
        }

        return 0;
    }
}
