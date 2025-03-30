<?php

namespace App\Member\Actions;

use App\Actions\PullMemberAction;
use App\Actions\PullMembershipsAction;
use App\Activity;
use App\Confession;
use App\Member\Member;
use App\Setting\NamiSettings;
use App\Subactivity;
use Lorisleiva\Actions\Concerns\AsAction;
use Zoomyboy\LaravelNami\Data\BankAccount;
use Zoomyboy\LaravelNami\Data\Member as NamiMember;

class NamiPutMemberAction
{
    use AsAction;

    public function handle(Member $member, ?Activity $activity = null, ?Subactivity $subactivity = null): void
    {
        $api = app(NamiSettings::class)->login();
        $namiMember = NamiMember::from([
            'firstname' => $member->firstname,
            'lastname' => $member->lastname,
            'joinedAt' => $member->joined_at,
            'birthday' => $member->birthday,
            'sendNewspaper' => $member->send_newspaper,
            'address' => $member->address,
            'zip' => $member->zip,
            'location' => $member->location,
            'nickname' => $member->nickname,
            'otherCountry' => $member->other_country,
            'furtherAddress' => $member->further_address,
            'mainPhone' => $member->main_phone,
            'mobilePhone' => $member->mobile_phone,
            'workPhone' => $member->work_phone,
            'fax' => $member->fax,
            'email' => $member->email,
            'emailParents' => $member->email_parents,
            'genderId' => optional($member->gender)->nami_id,
            'confessionId' => $member->confession ? $member->confession->nami_id : Confession::firstWhere('is_null', true)->nami_id,
            'regionId' => optional($member->region)->nami_id,
            'countryId' => $member->country->nami_id,
            'feeId' => $member->getNamiFeeId(),
            'nationalityId' => $member->nationality->nami_id,
            'groupId' => $member->group->nami_id,
            'id' => $member->nami_id,
            'version' => $member->version,
            'keepdata' => $member->keepdata,
            'bankAccount' => BankAccount::from([]),
        ]);
        $response = $api->putMember($namiMember, $activity ? $activity->nami_id : null, $subactivity ? $subactivity->nami_id : null);
        Member::withoutEvents(function () use ($response, $member) {
            $member->update(['nami_id' => $response]);
            app(PullMemberAction::class)->handle($member->group->nami_id, $member->nami_id);
            app(PullMembershipsAction::class)->handle($member);
        });
    }
}
