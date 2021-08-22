<?php

namespace App\Membership;

use App\Activity;
use App\Http\Controllers\Controller;
use App\Member\Member;
use App\Subactivity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MembershipController extends Controller
{
    public function store(Member $member, Request $request): RedirectResponse
    {
        $namiId = auth()->user()->api()->group($member->group->nami_id)->member($member->nami_id)
            ->putMembership([
                'created_at' => now(),
                'group_id' => $member->group->nami_id,
                'activity_id' => Activity::find($request->input('activity_id'))->nami_id,
                'subactivity_id' => optional(Subactivity::find($request->input('subactivity_id')))->nami_id,
            ]);

        $membership = $member->memberships()->create(array_merge(
            $request->input(),
            ['nami_id' => $namiId],
        ));

        return redirect()->back();
    }
}
