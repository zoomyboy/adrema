<?php

namespace App\Actions;

use App\Activity;
use App\Group;
use App\Initialize\ActivityCreator;
use App\Member\Member;
use App\Member\Membership;
use App\Setting\NamiSettings;
use App\Subactivity;
use Illuminate\Support\Collection;
use Lorisleiva\Actions\Concerns\AsAction;
use Zoomyboy\LaravelNami\Api;
use Zoomyboy\LaravelNami\Data\MembershipEntry as NamiMembershipEntry;

class InsertMembershipsAction
{
    use AsAction;

    /**
     * @param Collection<int, NamiMembershipEntry> $memberships
     */
    public function handle(Member $member, Collection $memberships): void
    {
        if (!$member->hasNami) {
            return;
        }

        foreach ($memberships as $membership) {
            $existingMembership = Membership::where('nami_id', $membership->id)->first();

            $group = Group::where('name', $membership->group)->whereNotNull('nami_id')->first();
            if (!$group) {
                continue;
            }

            if (null !== $this->overviewStrategy($member, $group, $membership)) {
                continue;
            }

            $this->singleStrategy($member, $group, $membership);
        }

        $membershipIds = $memberships->map(fn ($membership) => $membership->id)->toArray();
        $member->memberships()->whereNotIn('nami_id', $membershipIds)->whereNotNull('nami_id')->delete();
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
            $subactivity = Subactivity::remote()->where('name', $membership->subactivity)->first();

            if (!$subactivity) {
                return null;
            }
        } else {
            $subactivity = null;
        }

        return $member->memberships()->updateOrCreate(['nami_id' => $membership->id], [
            'nami_id' => $membership->id,
            'from' => $membership->startsAt,
            'to' => $membership->endsAt,
            'group_id' => $group->id,
            'activity_id' => $activity->id,
            'subactivity_id' => $subactivity?->id,
        ]);
    }

    private function singleStrategy(Member $member, Group $group, NamiMembershipEntry $membershipEntry): ?Membership
    {
        $membership = $this->api()->membership($member->nami_id, $membershipEntry->id);
        app(ActivityCreator::class)->createFor($this->api(), $membership->group());

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
            'to' => $membership->endsAt,
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
