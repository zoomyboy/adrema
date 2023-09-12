<?php

namespace App\Membership\Actions;

use App\Maildispatcher\Actions\ResyncAction;
use App\Member\Member;
use App\Member\Membership;
use App\Setting\NamiSettings;
use Illuminate\Http\JsonResponse;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class MembershipDestroyAction
{
    use AsAction;

    public function handle(Member $member, Membership $membership, NamiSettings $settings): void
    {
        $api = $settings->login();

        if ($membership->hasNami) {
            $settings->login()->deleteMembership(
                $member->nami_id,
                $api->membership($member->nami_id, $membership->nami_id)
            );
        }

        $membership->delete();

        if ($membership->hasNami) {
            $member->syncVersion();
        }
    }

    public function asController(Membership $membership, NamiSettings $settings): JsonResponse
    {
        $this->handle(
            $membership->member,
            $membership,
            $settings,
        );

        ResyncAction::dispatch();
        return response()->json([]);
    }
}
