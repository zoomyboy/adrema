<?php

namespace App\Member\Actions;

use App\Actions\MemberPullAction;
use App\Activity;
use App\Confession;
use App\Member\Member;
use App\Setting\NamiSettings;
use App\Subactivity;
use Lorisleiva\Actions\Concerns\AsAction;

class NamiPutMemberAction
{
    use AsAction;

    public function handle(Member $member, ?Activity $activity = null, ?Subactivity $subactivity = null): void
    {
        $api = app(NamiSettings::class)->login();
        $response = $api->putMember([
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
            'gender_id' => optional($member->gender)->nami_id,
            'confession_id' => $member->confession ? $member->confession->nami_id : Confession::firstWhere('is_null', true)->nami_id,
            'region_id' => optional($member->region)->nami_id,
            'country_id' => $member->country->nami_id,
            'fee_id' => $member->getNamiFeeId(),
            'nationality_id' => $member->nationality->nami_id,
            'group_id' => $member->group->nami_id,
            'first_activity_id' => $activity ? $activity->nami_id : null,
            'first_subactivity_id' => $subactivity ? $subactivity->nami_id : null,
            'id' => $member->nami_id,
            'version' => $member->version,
        ]);
        Member::withoutEvents(function () use ($response, $member, $api) {
            $member->update(['nami_id' => $response['id']]);
            app(MemberPullAction::class)->api($api)->member($member->group->nami_id, $member->nami_id)->execute();
        });
    }
}
