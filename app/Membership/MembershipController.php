<?php

namespace App\Membership;

use App\Http\Controllers\Controller;
use App\Member\Member;
use App\Member\Membership;
use App\Membership\Requests\StoreRequest;
use App\Setting\NamiSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MembershipController extends Controller
{

    public function store(Member $member, StoreRequest $request, NamiSettings $settings): RedirectResponse
    {
        $request->persist($member, $settings);

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
