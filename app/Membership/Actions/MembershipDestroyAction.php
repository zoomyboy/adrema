<?php

namespace App\Membership\Actions;

use App\Member\Member;
use App\Member\Membership;
use App\Setting\NamiSettings;
use Illuminate\Http\RedirectResponse;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class MembershipDestroyAction
{
    use AsAction;

    public function handle(Member $member, Membership $membership, NamiSettings $settings): void
    {
        $api = $settings->login();
        $settings->login()->deleteMembership(
            $member->nami_id,
            $api->membership($member->nami_id, $membership->nami_id)
        );
        $membership->delete();
        $member->syncVersion();
    }

    public function asController(Member $member, Membership $membership, ActionRequest $request, NamiSettings $settings): RedirectResponse
    {
        $this->handle(
            $member,
            $membership,
            $settings,
        );

        return redirect()->back();
    }
}
