<?php

namespace App\Prevention\Actions;

use Inertia\Inertia;
use Inertia\Response;
use Lorisleiva\Actions\Concerns\AsAction;

class PreventionIndexAction
{
    use AsAction;

    public function handle(): Response
    {
        session()->put('menu', 'setting');
        session()->put('title', 'Prävention');

        return Inertia::render('setting/Prevention');
    }
}
