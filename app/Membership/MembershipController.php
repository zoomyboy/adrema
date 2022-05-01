<?php

namespace App\Membership;

use App\Http\Controllers\Controller;
use App\Member\Member;
use App\Member\Membership;
use App\Membership\Requests\StoreRequest;
use App\Setting\NamiSettings;
use Illuminate\Http\RedirectResponse;

class MembershipController extends Controller
{
    public function store(Member $member, StoreRequest $request, NamiSettings $settings): RedirectResponse
    {
        $request->persist($member, $settings);

        return redirect()->back();
    }

    public function destroy(Member $member, Membership $membership, NamiSettings $settings): RedirectResponse
    {
        $api = $settings->login();
        $api->deleteMembership(
            $member->nami_id,
            $api->membership($member->nami_id, $membership->nami_id)
        );
        $membership->delete();
        $member->syncVersion();

        return redirect()->back();
    }
}
