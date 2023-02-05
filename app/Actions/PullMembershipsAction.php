<?php

namespace App\Actions;

use App\Activity;
use App\Group;
use App\Initialize\ActivityCreator;
use App\Member\Member;
use App\Member\Membership;
use App\Setting\NamiSettings;
use App\Subactivity;
use Lorisleiva\Actions\Concerns\AsAction;
use Zoomyboy\LaravelNami\Api;
use Zoomyboy\LaravelNami\Data\MembershipEntry as NamiMembershipEntry;

class PullMembershipsAction
{
    use AsAction;

    public function handle(Member $member): void
    {
        if (!$member->hasNami) {
            return;
        }

        $memberships = $this->api()->membershipsOf($member->nami_id);

        foreach ($memberships as $membership) {
            $existingMembership = Membership::where('nami_id', $membership->id)->first();

            $group = Group::where('name', $membership->group)->first();
            if (!$group) {
                continue;
            }

            if (null !== $this->overviewStrategy($member, $group, $membership)) {
                continue;
            }

            $this->singleStrategy($member, $group, $membership);
        }
    }

    private function overviewStrategy(Member $member, Group $group, NamiMembershipEntry $membership): ?Membership
    {
        $activity = 1 === preg_match('/\(([0-9]+)\)/', $membership->activity, $activityMatches)
            ? Activity::where('nami_id', (int) $activityMatches[1])->first()
            : null;

        if (!$activity) {
            return null;
        }

        if (null !== $membership->subactivity) {
            $subactivity = Subactivity::where('name', $membership->subactivity)->first();

            if (!$subactivity) {
                return null;
            }
        } else {
            $subactivity = null;
        }

        return $member->memberships()->updateOrCreate(['nami_id' => $membership->id], [
            'nami_id' => $membership->id,
            'from' => $membership->startsAt,
            'group_id' => $group->id,
            'activity_id' => $activity->id,
            'subactivity_id' => $subactivity?->id,
        ]);
    }

    private function singleStrategy(Member $member, Group $group, NamiMembershipEntry $membershipEntry): ?Membership
    {
        $membership = $this->api()->membership($member->nami_id, $membershipEntry->id);
        app(ActivityCreator::class)->createFor($this->api(), $membership->groupId);

        $activity = Activity::nami($membership->activityId);
        if (!$activity) {
            return null;
        }

        if (null !== $membership->subactivityId) {
            $subactivity = Subactivity::nami($membership->subactivityId);

            if (!$subactivity) {
                return null;
            }
        } else {
            $subactivity = null;
        }

        return $member->memberships()->updateOrCreate(['nami_id' => $membership->id], [
            'nami_id' => $membership->id,
            'from' => $membership->startsAt,
            'group_id' => $group->id,
            'activity_id' => $activity->id,
            'subactivity_id' => $subactivity?->id,
        ]);
    }

    private function api(): Api
    {
        return app(NamiSettings::class)->login();
    }
}
