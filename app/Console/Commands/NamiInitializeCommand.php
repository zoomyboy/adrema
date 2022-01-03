<?php

namespace App\Console\Commands;

use App\Initialize\Initializer;
use Illuminate\Console\Command;
use Zoomyboy\LaravelNami\Nami;
use Zoomyboy\LaravelNami\NamiUser;

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
        app(Initializer::class)->run(NamiUser::fromPayload([
            'credentials' => [
                'mglnr' => $this->option('mglnr'),
                'password' => $this->option('password'),
            ],
            'firstname' => 'Console',
            'lastname' => 'Console',
            'group_id' => $this->option('group_id'),
        ]));

        return 0;
    }
}
