<?php 

namespace App\Initialize;

class InitializeMembers {

    private $bar;
    private $api;
    
    public function __construct($bar, $api) {
        $this->bar = $bar;
        $this->api = $api;
    }

    public function handle() {
        $allMembers = collect([]);

        $this->bar->task('Finde Mitglieder', function() use (&$allMembers) {
            $allMembers = collect($this->api->allMembers()->data);
        });

        $this->bar->tasks($allMembers, function($member) {
            return "Synchronisiere {$member->entries_vorname} {$member->entries_nachname}";
        }, function($member) {
            $data = $this->api->getMember($member->id)->data;
            $gender = \App\Gender::where('nami_id', $data->geschlechtId)->where('is_null', false)->first();

            $confession = $data->konfessionId
                ? \App\Confession::where('nami_id', $data->konfessionId)->first()
                : null
            ;
            $region = \App\Region::where('nami_id', $data->regionId)->where('is_null', false)->first();
            $country = \App\Country::where('nami_id', $data->landId)->first();
            $nationality = \App\Nationality::where('nami_id', $data->staatsangehoerigkeitId)->first();

            $sub = null;

            $attributes = [
                'firstname' => $data->vorname,
                'lastname' => $data->nachname,
                'nickname' => $data->spitzname,
                'joined_at' => $data->eintrittsdatum,
                'birthday' => $data->geburtsDatum,
                'keepdata' => $data->wiederverwendenFlag,
                'sendnewspaper' => $data->zeitschriftenversand,
                'address' => $data->strasse,
                'zip' => $data->plz,
                'city' => $data->ort,
                'nickname' => $data->spitzname,
                'other_country' => $data->staatsangehoerigkeitText,
                'further_address' => $data->nameZusatz,
                'phone' => $data->telefon1,
                'mobile' => $data->telefon2,
                'business_phone' => $data->telefon3,
                'fax' => $data->telefax,
                'email' => $data->email,
                'email_parents' => $data->emailVertretungsberechtigter,
                'nami_id' => $data->id,
                'active' => $data->status == 'Aktiv'
            ];

            $m = new \App\Member\Member($attributes);

            $m->gender()->associate($gender);
            $m->country()->associate($country);
            $m->region()->associate($region);
            // $m->way()->associate(\Setting::get('defaultWay'));
            $m->confession()->associate($confession);
            $m->nationality()->associate($nationality);

            $m->save();
        });
    }
}
