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
        return Inertia::render('group/Index', [
            'data' => GroupResource::collection($this->handle()),
        ]);
    }
}
