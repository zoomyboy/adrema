<?php

namespace App\Membership\Actions;

use App\Member\Data\MembershipData;
use App\Membership\FilterScope;
use Inertia\Inertia;
use Inertia\Response;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class MembershipIndexAction
{
    use AsAction;

    public function asController(ActionRequest $request): Response
    {
        return Inertia::render(
            'membership/Index',
            ['data' => MembershipData::collectPages(FilterScope::fromRequest($request->input('filter', ''))->getQuery()->paginate(20))]
        );
    }
}
