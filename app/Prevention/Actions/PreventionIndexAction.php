<?php

namespace App\Prevention\Actions;

use Inertia\Inertia;
use Lorisleiva\Actions\Concerns\AsAction;

class PreventionIndexAction
{
    use AsAction;

    public function handle()
    {
        session()->put('menu', 'setting');
        session()->put('title', 'Prävention');

        return Inertia::render('setting/Prevention');
    }
}
