<?php

namespace App\Form\Actions;

use App\Form\FormSettings;
use Illuminate\Support\Facades\Http;
use Lorisleiva\Actions\Concerns\AsAction;

class ClearFrontendCacheAction
{
    use AsAction;

    public function handle(): void
    {
        Http::get(app(FormSettings::class)->clearCacheUrl);
    }
}
