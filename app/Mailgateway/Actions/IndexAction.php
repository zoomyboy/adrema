<?php

namespace App\Mailgateway\Actions;

use App\Mailgateway\Models\Mailgateway;
use App\Mailgateway\Resources\MailgatewayResource;
use Illuminate\Database\Eloquent\Builder;
use Inertia\Inertia;
use Inertia\Response;
use Lorisleiva\Actions\Concerns\AsAction;

class IndexAction
{
    use AsAction;

    /**
     * @return Builder<Mailgateway>
     */
    public function handle(): Builder
    {
        return (new Mailgateway())->newQuery();
    }

    public function asController(): Response
    {
        session()->put('menu', 'setting');
        session()->put('title', 'E-Mail-Verbindungen');

        return Inertia::render('mailgateway/Index', [
            'data' => MailgatewayResource::collection($this->handle()->paginate(10)),
        ]);
    }
}
