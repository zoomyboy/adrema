<?php 

namespace App\Initialize;

use App\Gender;
use App\Confession;
use App\Country;
use App\Member\Member;
use App\Region;
use App\Nationality;

class InitializeMembers {

    private $bar;
    private $api;
    
    public function __construct($bar, $api) {
        $this->bar = $bar;
        $this->api = $api;
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
                    'sendnewspaper' => true,           // @todo implement in nami api
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
                    'gender_id' => Gender::firstOrFail('nami_id', $member->gender_id)->id,
                    'confession_id' => optional(Confession::firstWhere('nami_id', $member->confession_id))->id,
                    'region_id' => 1,       // @todo implement in nami api
                    'country_id' => 1,      // @todo implement in nami api
                    'nationality_id' => 1,  // @todo implement in nami api
                ]);
            });
        });
    }
}
