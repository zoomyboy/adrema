<?php

namespace App\Fileshare\Actions;

use App\Fileshare\Models\FileshareConnection;
use App\Fileshare\Resources\FileshareConnectionResource;
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

        return Inertia::render('fileshareconnection/Index', [
            'data' => FileshareConnectionResource::collection(FileshareConnection::paginate(15)),
        ]);
    }
}
