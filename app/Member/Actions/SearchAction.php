<?php

namespace App\Member\Actions;

use App\Member\FilterScope;
use App\Member\Member;
use App\Member\MemberResource;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class SearchAction
{
    use AsAction;

    /**
     * @return Collection<int, Member>
     */
    public function handle(FilterScope $filter): Collection
    {
        return Member::search($filter->search)->query(fn ($q) => $q->select('*')
            ->withFilter($filter)
            ->ordered()
        )->get();
    }

    public function asController(ActionRequest $request): AnonymousResourceCollection
    {
        return MemberResource::collection($this->handle(FilterScope::fromRequest($request->input('filter', ''))));
    }
}
