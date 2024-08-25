<?php

namespace App\Group\Actions;

use App\Group;
use App\Group\Resources\GroupResource;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class GroupApiIndexAction
{
    use AsAction;

    /**
     * @return Collection<int, Group>
     */
    public function handle(): Collection
    {
        return Group::get();
    }

    public function asController(ActionRequest $request, ?Group $group = null): AnonymousResourceCollection
    {
        return GroupResource::collection(
            $request->has('all')
                ? Group::with('children')->get()
                : ($group ? $group->children()->withCount('children')->get() : Group::where('parent_id', null)->withCount('children')->get())
        );
    }
}
