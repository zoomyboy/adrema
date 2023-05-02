<?php

namespace App\Activity\Actions;

use App\Activity;
use App\Activity\Resources\ActivityResource;
use App\Http\Views\ActivityFilterScope;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Inertia\Inertia;
use Inertia\Response;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class IndexAction
{
    use AsAction;

    public function handle(ActivityFilterScope $filter): AnonymousResourceCollection
    {
        return ActivityResource::collection(Activity::withFilter($filter)->paginate(20));
    }

    public function asController(ActionRequest $request): Response
    {
        session()->put('menu', 'activity');
        session()->put('title', 'Tätigkeiten');
        $filter = ActivityFilterScope::fromRequest($request->input('filter'));

        return Inertia::render('activity/VIndex', [
            'data' => $this->handle($filter),
        ]);
    }
}
