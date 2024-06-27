<?php

namespace App\Fileshare\Actions;

use App\Fileshare\Models\Fileshare;
use App\Fileshare\Resources\FileshareResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Lorisleiva\Actions\Concerns\AsAction;

class FileshareApiIndexAction
{
    use AsAction;

    public function handle(): AnonymousResourceCollection
    {
        session()->put('menu', 'setting');
        session()->put('title', 'Datei-Verbindungen');

        return FileshareResource::collection(Fileshare::paginate(15));
    }
}
