<?php

namespace App\Membership\Actions;

use App\Member\Data\MembershipData;
use App\Member\Membership;
use Inertia\Inertia;
use Inertia\Response;
use Lorisleiva\Actions\Concerns\AsAction;
use Spatie\LaravelData\PaginatedDataCollection;

class MembershipIndexAction
{
    use AsAction;

    public function asController(): Response
    {
        return Inertia::render(
            'membership/Index',
            MembershipData::collect(Membership::orderByRaw('member_id, activity_id, subactivity_id')->paginate(20), PaginatedDataCollection::class)->wrap('data')
        );
    }
}
