<?php

namespace App\Membership\Actions;

use App\Member\Member;
use App\Member\Membership;
use App\Membership\MembershipResource;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class ApiIndexAction
{
    use AsAction;

    /**
     * @return Collection<int, Membership>
     */
    public function handle(Member $member): Collection
    {
        return $member->memberships;
    }

    public function asController(Member $member): AnonymousResourceCollection
    {
        return MembershipResource::collection($this->handle($member))
            ->additional([
                'meta' => MembershipResource::memberMeta($member)
            ]);
    }
}
