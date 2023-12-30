<?php

namespace App\Membership\Actions;

use App\Activity;
use App\Group;
use Inertia\Inertia;
use Inertia\Response;
use Lorisleiva\Actions\Concerns\AsAction;

class MassListAction
{
    use AsAction;

    public function asController(): Response
    {
        session()->put('menu', 'activity');
        session()->put('title', 'Mitgliedschaften zuweisen');
        $activities = Activity::with('subactivities')->get();

        return Inertia::render('activity/Masslist', [
            'activities' => $activities->pluck('name', 'id'),
            'subactivities' => $activities->mapWithKeys(fn (Activity $activity) => [$activity->id => $activity->subactivities()->pluck('name', 'id')]),
            'groups' => Group::pluck('name', 'id'),
        ]);
    }
}
