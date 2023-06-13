<?php

namespace App\Maildispatcher\Actions;

use App\Maildispatcher\Models\Maildispatcher;
use App\Maildispatcher\Resources\MaildispatcherResource;
use Inertia\Inertia;
use Inertia\Response;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class IndexAction
{
    use AsAction;

    public function asController(ActionRequest $request): Response
    {
        session()->put('menu', 'maildispatcher');
        session()->put('title', 'Mail-Verteiler');

        return Inertia::render('maildispatcher/MaildispatcherIndex', [
            'data' => MaildispatcherResource::collection(Maildispatcher::with('gateway')->paginate(10)),
        ]);
    }
}
