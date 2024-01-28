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
     * @param array<string, mixed> $filter
     * @return LengthAwarePaginator<int, Member>
     */
    public function handle(array $filter, int $perPage): LengthAwarePaginator
    {
        return FilterScope::fromPost($filter)->getQuery()->paginate($perPage);
    }

    public function asController(ActionRequest $request): AnonymousResourceCollection
    {
        return MemberResource::collection($this->handle($request->input('filter', []), $request->input('per_page', 15)));
    }
}
