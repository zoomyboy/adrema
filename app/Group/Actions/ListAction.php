<?php

namespace App\Group\Actions;

use App\Activity;
use App\Group;
use Inertia\Inertia;
use Inertia\Response;
use Lorisleiva\Actions\Concerns\AsAction;

class ListAction
{
    use AsAction;

    public function asController(): Response
    {
        session()->put('menu', 'group');
        session()->put('title', 'Gruppen');
        $activities = Activity::with('subactivities')->get();

        return Inertia::render('group/Index', [
            'activities' => $activities->pluck('name', 'id'),
            'subactivities' => $activities->mapWithKeys(fn (Activity $activity) => [$activity->id => $activity->subactivities()->pluck('name', 'id')]),
            'groups' => Group::pluck('name', 'id'),
        ]);
    }
}
