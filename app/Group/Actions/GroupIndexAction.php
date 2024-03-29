<?php

namespace App\Group\Actions;

use App\Group;
use App\Group\Resources\GroupResource;
use Illuminate\Database\Eloquent\Collection;
use Inertia\Inertia;
use Inertia\Response;
use Lorisleiva\Actions\Concerns\AsAction;

class GroupIndexAction
{
    use AsAction;

    /**
     * @return Collection<int, Group>
     */
    public function handle(): Collection
    {
        return Group::get();
    }

    public function asController(): Response
    {
        session()->put('menu', 'group');
        session()->put('title', 'Gruppierungen');

        return Inertia::render('group/Index', [
            'data' => GroupResource::collection(Group::where('parent_id', null)->withCount('children')->get()),
        ]);
    }
}
