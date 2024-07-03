<?php

namespace App\Actions;

use DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Laravel\Telescope\Console\PruneCommand;
use Lorisleiva\Actions\Concerns\AsAction;

class DbMaintainAction
{
    use AsAction;

    public string $commandSignature = 'db:maintain';

    public function handle()
    {
        Artisan::call(PruneCommand::class, ['--hours' => 168]);     // 168h = 7 Tage
        DB::select('optimize table telescope_entries');
        Http::post('https://zoomyboy.de/maintain');
    }
}
