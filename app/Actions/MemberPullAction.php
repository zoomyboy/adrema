<?php

namespace App\Actions;

use App\Activity;
use App\Confession;
use App\Country;
use App\Course\Models\Course;
use App\Fee;
use App\Gender;
use App\Group;
use App\Initialize\ActivityCreator;
use App\Member\Member;
use App\Nationality;
use App\Region;
use App\Subactivity;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Zoomyboy\LaravelNami\Api;
use Zoomyboy\LaravelNami\Data\MembershipEntry;
use Zoomyboy\LaravelNami\Exceptions\RightException;
use Zoomyboy\LaravelNami\Member as NamiMember;
use Zoomyboy\LaravelNami\NamiException;

class MemberPullAction
{
    private NamiMember $member;
    private Api $api;

    public function api(Api $api): self
    {
        $this->api = $api;

        return $this;
    }

    public function member(int $groupId, int $memberId): self
    {
        $this->member = NamiMember::fromNami($this->api->member($groupId, $memberId));

        return $this;
    }

    public function execute(): void
    {
        if (!$this->member->joined_at) {
            return;
        }

        try {
            $m = Member::updateOrCreate(['nami_id' => $this->member->id], [
                'firstname' => $this->member->firstname,
                'lastname' => $this->member->lastname,
                'joined_at' => $this->member->joined_at,
                'birthday' => $this->member->birthday,
                'send_newspaper' => $this->member->send_newspaper,
                'address' => $this->member->address,
                'zip' => $this->member->zip,
                'location' => $this->member->location,
                'nickname' => $this->member->nickname,
                'other_country' => $this->member->other_country,
                'further_address' => $this->member->further_address,
                'main_phone' => $this->member->main_phone,
                'mobile_phone' => $this->member->mobile_phone,
                'work_phone' => $this->member->work_phone,
                'fax' => $this->member->fax,
                'email' => $this->member->email,
                'email_parents' => $this->member->email_parents,
                'nami_id' => $this->member->id,
                'group_id' => Group::firstOrCreate(['nami_id' => $this->member->group_id], ['nami_id' => $this->member->group_id, 'name' => $this->member->group_name])->id,
                'gender_id' => optional(Gender::firstWhere('nami_id', $this->member->gender_id ?: -1))->id,
                'confession_id' => optional(Confession::firstWhere('nami_id', $this->member->confession_id ?: -1))->id,
                'region_id' => optional(Region::firstWhere('nami_id', $this->member->region_id ?: -1))->id,
                'country_id' => optional(Country::where('nami_id', $this->member->country_id)->first())->id,
                'subscription_id' => $this->getSubscriptionId($this->member),
                'nationality_id' => Nationality::where('nami_id', $this->member->nationality_id)->firstOrFail()->id,
                'version' => $this->member->version,
            ]);

            try {
                foreach ($this->api->coursesFor($this->member->id) as $course) {
                    $m->courses()->updateOrCreate(['nami_id' => $course->id], [
                        'course_id' => Course::where('nami_id', $course->courseId)->firstOrFail()->id,
                        'organizer' => $course->organizer,
                        'event_name' => $course->eventName,
                        'completed_at' => $course->completedAt,
                        'nami_id' => $course->id,
                    ]);
                }
            } catch (RightException $e) {
            }

            try {
                foreach ($this->api->membershipsOf($this->member->id) as $membership) {
                    if (null !== $membership->endsAt) {
                        continue;
                    }
                    try {
                        [$activityId, $subactivityId, $groupId] = $this->fetchMembership($membership);
                    } catch (RightException $e) {
                        continue;
                    }
                    if (is_null($activityId)) {
                        continue;
                    }
                    $m->memberships()->updateOrCreate(['nami_id' => $membership->id], [
                        'nami_id' => $membership->id,
                        'from' => $membership->startsAt,
                        'group_id' => $groupId,
                        'activity_id' => $activityId,
                        'subactivity_id' => $subactivityId,
                    ]);
                }
            } catch (RightException $e) {
            }
        } catch (ModelNotFoundException $e) {
            dd($e->getMessage(), $this->member);
        }
    }

    private function fetchMembership(MembershipEntry $membershipEntry): array
    {
        if ($this->shouldSyncMembership($membershipEntry)) {
            $membership = $this->api->membership($this->member->id, $membershipEntry->id);

            if (is_null($membership)) {
                return [null, null, null];
            }
            app(ActivityCreator::class)->createFor($this->api, $membership->groupId);
            $group = Group::firstOrCreate(['nami_id' => $membership->groupId], [
            'nami_id' => $membership->groupId,
            'name' => $membership->group,
        ]);
            try {
                $activityId = Activity::where('nami_id', $membership->activityId)->firstOrFail()->id;
                $subactivityId = $membership->subactivityId
                ? Subactivity::where('nami_id', $membership->subactivityId)->firstOrFail()->id
                : null;

                return [$activityId, $subactivityId, $group->id];
            } catch (ModelNotFoundException $e) {
                return [null, null, null];
            }
        }

        if (null === $membershipEntry->subactivity) {
            $subactivityId = null;
        } else {
            $subactivityId = Subactivity::where('name', $membershipEntry->subactivity)->firstOrFail()->id;
        }
        preg_match('/\(([0-9]+)\)$/', $membershipEntry->activity, $activityMatches);
        $activityId = Activity::where('nami_id', $activityMatches[1])->firstOrFail()->id;
        $groupId = Group::where('name', $membershipEntry->group)->firstOrFail()->id;

        return [$activityId, $subactivityId, $groupId];
    }

    private function shouldSyncMembership(MembershipEntry $membershipEntry): bool
    {
        if (!Group::where('name', $membershipEntry->group)->exists()) {
            return true;
        }
        if (1 !== preg_match('/\(([0-9]+)\)/', $membershipEntry->activity, $activityMatches)) {
            throw new NamiException("ID in taetigkeit string not found: {$membershipEntry->activity}");
        }

        if (!Activity::where('nami_id', (int) $activityMatches[1])->exists()) {
            return true;
        }

        if (null === $membershipEntry->subactivity) {
            return false;
        }

        return !Subactivity::where('name', $membershipEntry->subactivity)->exists();
    }

    public function getSubscriptionId(NamiMember $member): ?int
    {
        $fee = Fee::firstWhere('nami_id', $member->fee_id ?: -1);
        if (is_null($fee)) {
            return null;
        }

        return optional($fee->subscriptions()->first())->id;
    }
}
