<?php

namespace App\Activity\Actions;

use App\Activity;
use App\Activity\Resources\ActivityResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Inertia\Inertia;
use Inertia\Response;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class IndexAction
{
    use AsAction;

    public function handle(): AnonymousResourceCollection
    {
        return ActivityResource::collection(Activity::local()->paginate(20));
    }

    public function asController(ActionRequest $request): Response
    {
        return Inertia::render('activity/Index', [
            'data' => $this->handle(),
        ]);
    }
}
