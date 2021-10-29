<?php

namespace App\Membership;

use App\Activity;
use App\Http\Controllers\Controller;
use App\Member\Member;
use App\Member\Membership;
use App\Subactivity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MembershipController extends Controller
{

    public function store(Member $member, Request $request): RedirectResponse
    {
        $namiId = auth()->user()->api()->group($member->group->nami_id)->member($member->nami_id)
            ->putMembership([
                'starts_at' => now(),
                'group_id' => $member->group->nami_id,
                'activity_id' => Activity::find($request->input('activity_id'))->nami_id,
                'subactivity_id' => optional(Subactivity::find($request->input('subactivity_id')))->nami_id,
            ]);

        $member->memberships()->create(array_merge(
            $request->input(),
            ['nami_id' => $namiId],
        ));

        $member->syncVersion(auth()->user()->api());

        return redirect()->back();
    }

    public function destroy(Member $member, Membership $membership): RedirectResponse
    {
        auth()->user()->api()->group($member->group->nami_id)->member($member->nami_id)
            ->deleteMembership($membership->nami_id);

        $membership->delete();
        $member->syncVersion(auth()->user()->api());

        return redirect()->back();
    }

}
