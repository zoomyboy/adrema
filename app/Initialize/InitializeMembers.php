<?php

namespace App\Initialize;

use App\Activity;
use App\Confession;
use App\Country;
use App\Course\Models\Course;
use App\Fee;
use App\Gender;
use App\Group;
use App\Member\Member;
use App\Nationality;
use App\Region;
use App\Subactivity;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Zoomyboy\LaravelNami\Api;
use Zoomyboy\LaravelNami\Data\MembershipEntry;
use Zoomyboy\LaravelNami\Exceptions\RightException;
use Zoomyboy\LaravelNami\Member as NamiMember;
use Zoomyboy\LaravelNami\Membership as NamiMembership;
use Zoomyboy\LaravelNami\NamiException;

class InitializeMembers {

    private Api $api;

    public function __construct(Api $api) {
        $this->api = $api;
    }

    public function getSubscriptionId(NamiMember $member): ?int
    {
        $fee = Fee::firstWhere('nami_id', $member->fee_id ?: -1);
        if (is_null($fee)) {
            return null;
        }

        return optional($fee->subscriptions()->first())->id;
    }

    public function handle(): void
    {
        $allMembers = collect([]);

        $this->api->search([])->each(function(NamiMember $member): void {
            $member = NamiMember::fromNami($this->api->member($member->group_id, $member->id));
            if (!$member->joined_at) {
                return;
            }
            try {
                $m = Member::create([
                    'firstname' => $member->firstname,
                    'lastname' => $member->lastname,
                    'joined_at' => $member->joined_at,
                    'birthday' => $member->birthday,
                    'send_newspaper' => $member->send_newspaper,
                    'address' => $member->address,
                    'zip' => $member->zip,
                    'location' => $member->location,
                    'nickname' => $member->nickname,
                    'other_country' => $member->other_country,
                    'further_address' => $member->further_address,
                    'main_phone' => $member->main_phone,
                    'mobile_phone' => $member->mobile_phone,
                    'work_phone' => $member->work_phone,
                    'fax' => $member->fax,
                    'email' => $member->email,
                    'email_parents' => $member->email_parents,
                    'nami_id' => $member->id,
                    'group_id' => Group::firstOrCreate(['nami_id' => $member->group_id], ['nami_id' => $member->group_id, 'name' => $member->group_name])->id,
                    'gender_id' => optional(Gender::firstWhere('nami_id', $member->gender_id ?: -1))->id,
                    'confession_id' => optional(Confession::firstWhere('nami_id', $member->confession_id ?: -1))->id,
                    'region_id' => optional(Region::firstWhere('nami_id', $member->region_id ?: -1))->id,
                    'country_id' => optional(Country::where('nami_id', $member->country_id)->first())->id,
                    'subscription_id' => $this->getSubscriptionId($member),
                    'nationality_id' => Nationality::where('nami_id', $member->nationality_id)->firstOrFail()->id,
                    'version' => $member->version,
                ]);

                try {
                    foreach ($this->api->coursesFor($member->id) as $course) {
                        $m->courses()->create([
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
                    foreach ($this->api->membershipsOf($member->id) as $membership) {
                        if ($membership->endsAt !== null) {
                            continue;
                        }
                        try {
                            [$activityId, $subactivityId, $groupId] = $this->fetchMembership($member, $membership);
                        } catch (RightException $e) {
                            continue;
                        }
                        if (is_null($activityId)) {
                            continue;
                        }
                        $m->memberships()->create([
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
                dd($e->getMessage(), $member);
            }
        });
    }

    private function fetchMembership(NamiMember $member, MembershipEntry $membershipEntry): array
    {
        if ($this->shouldSyncMembership($membershipEntry)) {
            $membership = $this->api->membership($member->id, $membershipEntry->id);
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

        if ($membershipEntry->subactivity === null) {
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
        if (preg_match('/\(([0-9]+)\)/', $membershipEntry->activity, $activityMatches) !== 1) {
            throw new NamiException("ID in taetigkeit string not found: {$membershipEntry->activity}");
        }

        if (!Activity::where('nami_id', (int) $activityMatches[1])->exists()) {
            return true;
        }

        if ($membershipEntry->subactivity === null) {
            return false;
        }

        return !Subactivity::where('name', $membershipEntry->subactivity)->exists();
    }
}
