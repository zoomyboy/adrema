<?php

namespace App\Maildispatcher\Actions;

use App\Maildispatcher\Resources\MaildispatcherResource;
use Inertia\Inertia;
use Inertia\Response;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateAction
{
    use AsAction;

    public function asController(): Response
    {
        session()->put('menu', 'maildispatcher');
        session()->put('title', 'Mail-Verteiler erstellen');

        return Inertia::render('maildispatcher/MaildispatcherForm', [
            'mode' => 'create',
            'meta' => MaildispatcherResource::meta(),
        ]);
    }
}
