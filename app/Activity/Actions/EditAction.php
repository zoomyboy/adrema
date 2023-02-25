<?php

namespace App\Activity\Actions;

use App\Activity;
use App\Activity\Resources\ActivityResource;
use Inertia\Inertia;
use Inertia\Response;
use Lorisleiva\Actions\Concerns\AsAction;

class EditAction
{
    use AsAction;

    public function handle(Activity $activity): Response
    {
        return Inertia::render('activity/VForm', [
            'meta' => ActivityResource::meta(),
            'data' => new ActivityResource($activity->load('subactivities')),
        ]);
    }
}
