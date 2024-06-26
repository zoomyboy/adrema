<?php

namespace App\Fileshare\Actions;

use App\Fileshare\Models\FileshareConnection;
use App\Fileshare\Resources\FileshareConnectionResource;
use Inertia\Inertia;
use Lorisleiva\Actions\Concerns\AsAction;

class IndexAction
{
    use AsAction;

    public function handle()
    {
        session()->put('menu', 'setting');
        session()->put('title', 'Datei-Verbindungen');

        return Inertia::render('fileshare/Index', [
            'data' => FileshareConnectionResource::collection(FileshareConnection::paginate(15)),
        ]);
    }
}
