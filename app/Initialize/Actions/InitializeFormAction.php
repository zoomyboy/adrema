<?php

namespace App\Initialize\Actions;

use Inertia\Inertia;
use Inertia\Response;
use Lorisleiva\Actions\Concerns\AsAction;

class InitializeFormAction
{
    use AsAction;

    public function asController(): Response
    {
        session()->put('title', 'Einrichtung');

        return Inertia::render('Initialize/VIndex');
    }
}
