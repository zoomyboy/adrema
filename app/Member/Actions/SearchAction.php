<?php

namespace App\Member\Actions;

use App\Member\FilterScope;
use App\Member\Member;
use App\Member\MemberResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class SearchAction
{
    use AsAction;

    /**
     * @return LengthAwarePaginator<int, Member>
     */
    public function handle(FilterScope $filter, int $perPage): LengthAwarePaginator
    {
        return Member::search($filter->search)->query(
            fn ($q) => $q->select('*')
                ->withFilter($filter)
                ->ordered()
        )->paginate($perPage);
    }

    public function asController(ActionRequest $request): AnonymousResourceCollection
    {
        return MemberResource::collection($this->handle(FilterScope::fromRequest($request->input('filter', '')), $request->input('per_page', 15)));
    }
}
