<?php

namespace App\Maildispatcher\Actions;

use App\Maildispatcher\Models\Maildispatcher;
use App\Maildispatcher\Resources\MaildispatcherResource;
use Inertia\Inertia;
use Inertia\Response;
use Lorisleiva\Actions\Concerns\AsAction;

class EditAction
{
    use AsAction;

    public function asController(Maildispatcher $maildispatcher): Response
    {
        session()->put('menu', 'maildispatcher');
        session()->put('title', 'Mail-Verteiler bearbeiten');

        return Inertia::render('maildispatcher/MaildispatcherForm', [
            'data' => new MaildispatcherResource($maildispatcher),
            'meta' => MaildispatcherResource::meta(),
        ]);
    }
}
