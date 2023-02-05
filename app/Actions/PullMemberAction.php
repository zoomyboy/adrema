<?php

namespace App\Actions;

use App\Confession;
use App\Country;
use App\Fee;
use App\Gender;
use App\Group;
use App\Member\Member;
use App\Nationality;
use App\Region;
use App\Setting\NamiSettings;
use Lorisleiva\Actions\Concerns\AsAction;
use Zoomyboy\LaravelNami\Api;
use Zoomyboy\LaravelNami\Data\Member as NamiMember;

class PullMemberAction
{
    use AsAction;

    public function handle(int $groupId, int $memberId): void
    {
        $member = $this->api()->member($groupId, $memberId);

        if (!$member->joinedAt) {
            return;
        }

        $region = Region::firstWhere('nami_id', $member->regionId ?: -1);
        $m = Member::updateOrCreate(['nami_id' => $member->id], [
            'firstname' => $member->firstname,
            'lastname' => $member->lastname,
            'joined_at' => $member->joinedAt,
            'birthday' => $member->birthday,
            'send_newspaper' => $member->sendNewspaper,
            'address' => $member->address,
            'zip' => $member->zip,
            'location' => $member->location,
            'nickname' => $member->nickname,
            'other_country' => $member->otherCountry,
            'further_address' => $member->furtherAddress,
            'main_phone' => $member->mainPhone,
            'mobile_phone' => $member->mobilePhone,
            'work_phone' => $member->workPhone,
            'fax' => $member->fax,
            'email' => $member->email,
            'email_parents' => $member->emailParents,
            'nami_id' => $member->id,
            'group_id' => Group::firstOrCreate(['nami_id' => $member->groupId], ['nami_id' => $member->groupId, 'name' => $member->groupName])->id,
            'gender_id' => optional(Gender::firstWhere('nami_id', $member->genderId ?: -1))->id,
            'confession_id' => optional(Confession::firstWhere('nami_id', $member->confessionId ?: -1))->id,
            'region_id' => $region && !$region->is_null ? $region->id : null,
            'country_id' => optional(Country::where('nami_id', $member->countryId)->first())->id,
            'subscription_id' => $this->getSubscriptionId($member),
            'nationality_id' => Nationality::where('nami_id', $member->nationalityId)->firstOrFail()->id,
            'version' => $member->version,
        ]);
    }

    public function getSubscriptionId(NamiMember $member): ?int
    {
        $fee = Fee::firstWhere('nami_id', $member->feeId ?: -1);
        if (is_null($fee)) {
            return null;
        }

        return optional($fee->subscriptions()->first())->id;
    }

    private function api(): Api
    {
        return app(NamiSettings::class)->login();
    }
}
