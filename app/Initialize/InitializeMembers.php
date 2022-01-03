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
use Zoomyboy\LaravelNami\Exceptions\RightException;
use Zoomyboy\LaravelNami\Member as NamiMember;
use Zoomyboy\LaravelNami\NamiException;

class InitializeMembers {

    private $api;

    public function __construct($api) {
        $this->api = $api;
    }

    public function getSubscriptionId($member) {
        $fee = Fee::firstWhere('nami_id', $member->fee_id ?: -1);
        if (is_null($fee)) {
            return null;
        }

        return optional($fee->subscriptions()->first())->id;
    }

    public function handle() {
        $allMembers = collect([]);

        $this->api->search([])->each(function($member) {
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

                foreach ($this->api->coursesFor($member->id) as $course) {
                    $m->courses()->create([
                        'course_id' => Course::where('nami_id', $course->course_id)->firstOrFail()->id,
                        'organizer' => $course->organizer,
                        'event_name' => $course->event_name,
                        'completed_at' => $course->completed_at,
                        'nami_id' => $course->id,
                    ]);
                }

                foreach ($this->api->membershipsOf($member->id) as $membership) {
                    if ($membership['entries_aktivBis'] !== '') {
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
                        'nami_id' => $membership['id'],
                        'from' => $membership['entries_aktivVon'],
                        'group_id' => $groupId,
                        'activity_id' => $activityId,
                        'subactivity_id' => $subactivityId,
                    ]);
                }
            } catch (ModelNotFoundException $e) {
                dd($e->getMessage(), $member);
            }
        });
    }

    private function fetchMembership($member, $membership) {
        if ($this->shouldSyncMembership($membership)) {
            $singleMembership = $this->api->membership($member->id, $membership['id']);
            app(ActivityCreator::class)->createFor($this->api, $singleMembership['gruppierungId']);
            $group = Group::firstOrCreate(['nami_id' => $singleMembership['gruppierungId']], [
                'nami_id' => $singleMembership['gruppierungId'],
                'name' => $singleMembership['gruppierung'],
            ]);
            try {
                $activityId = Activity::where('nami_id', $singleMembership['taetigkeitId'])->firstOrFail()->id;
                $subactivityId = $singleMembership['untergliederungId']
                    ? Subactivity::where('nami_id', $singleMembership['untergliederungId'])->firstOrFail()->id
                    : null;
                return [$activityId, $subactivityId, $group->id];
            } catch (ModelNotFoundException $e) {
                return [null, null, null];
            }
        }

        if ($membership['entries_untergliederung'] === '') {
            $subactivityId = null;
        } else {
            $subactivityId = Subactivity::where('name', $membership['entries_untergliederung'])->firstOrFail()->id;
        }
        preg_match('/\(([0-9]+)\)$/', $membership['entries_taetigkeit'], $activityMatches);
        $activityId = Activity::where('nami_id', $activityMatches[1])->firstOrFail()->id;
        $groupId = Group::where('name', $membership['entries_gruppierung'])->firstOrFail()->id;

        return [$activityId, $subactivityId, $groupId];
    }

    private function shouldSyncMembership($membership) {
        if (!Group::where('name', $membership['entries_gruppierung'])->exists()) {
            return true;
        }
        if (preg_match('/\(([0-9]+)\)/', $membership['entries_taetigkeit'], $activityMatches) !== 1) {
            throw new NamiException("ID in taetigkeit string not found: {$membership['entries_taetigkeit']}");
        }

        if (!Activity::where('nami_id', (int) $activityMatches[1])->exists()) {
            return true;
        }

        if ($membership['entries_untergliederung'] === '') {
            return false;
        }

        return !Subactivity::where('name', $membership['entries_untergliederung'])->exists();
    }
}
