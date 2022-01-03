<?php 

namespace App\Initialize;

use Zoomyboy\LaravelNami\NamiUser;

class InitializeFees {

    private $api;
    
    public function __construct($api) {
        $this->api = $api;
    }

    public function handle(NamiUser $user) {
        $this->api->feesOf($user->getNamiGroupId())->each(function($fee) {
            \App\Fee::create(['nami_id' => $fee->id, 'name' => $fee->name])
                ->subscriptions()->create([
                    'name' => $fee->name,
                    'amount' => 1000,
                ]);
        });
    }
}
