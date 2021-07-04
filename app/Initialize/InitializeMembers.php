<?php 

namespace App\Initialize;

use App\Gender;
use App\Confession;
use App\Country;
use App\Member\Member;
use App\Region;
use App\Nationality;
use App\Fee;
use App\Group;
use App\Activity;

class InitializeMembers {

    private $bar;
    private $api;
    
    public function __construct($bar, $api) {
        $this->bar = $bar;
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

        $this->bar->task('Synchronisiere Mitglieder', function() {
            $this->api->group(auth()->user()->getNamiGroupId())->members()->each(function($member) {
                $m = Member::create([
                    'firstname' => $member->firstname,
                    'lastname' => $member->lastname,
                    'nickname' => $member->nickname,
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
                    'group_id' => Group::firstOrCreate(['nami_id' => $member->group_id], ['nami_id' => $member->group_id, 'name' => 'AAA'])->id,
                    'gender_id' => optional(Gender::firstWhere('nami_id', $member->gender_id ?: -1))->id,
                    'confession_id' => optional(Confession::firstWhere('nami_id', $member->confession_id ?: -1))->id,
                    'region_id' => optional(Region::firstWhere('nami_id', $member->region_id ?: -1))->id,
                    'country_id' => Country::where('nami_id', $member->country_id)->firstOrFail()->id,
                    'subscription_id' => $this->getSubscriptionId($member),
                    'nationality_id' => Nationality::where('nami_id', $member->nationality_id)->firstOrFail()->id,
                    'version' => $member->version,
                ]);
            });
        });
    }
}
