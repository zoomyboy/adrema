<?php

namespace App\Activity\Actions;

use App\Activity\Resources\ActivityResource;
use Inertia\Inertia;
use Inertia\Response;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateAction
{
    use AsAction;

    public function handle(): Response
    {
        session()->put('menu', 'activity');
        session()->put('title', 'Tätigkeit erstellen');

        return Inertia::render('activity/VForm', [
            'meta' => ActivityResource::meta(),
            'data' => [
                'name' => '',
                'is_filterable' => false,
                'subactivities' => [],
            ],
        ]);
    }
}
