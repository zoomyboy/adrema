<?php

namespace App\Fileshare\Actions;

use App\Fileshare\Models\Fileshare;
use App\Fileshare\Resources\FileshareResource;
use Inertia\Inertia;
use Inertia\Response;
use Lorisleiva\Actions\Concerns\AsAction;

class FileshareIndexAction
{
    use AsAction;

    public function handle(): Response
    {
        session()->put('menu', 'setting');
        session()->put('title', 'Datei-Verbindungen');

        return Inertia::render('fileshare/Index', [
            'data' => FileshareResource::collection(Fileshare::paginate(15)),
        ]);
    }
}
