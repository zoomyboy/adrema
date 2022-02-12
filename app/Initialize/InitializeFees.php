<?php 

namespace App\Initialize;

use Zoomyboy\LaravelNami\Api;
use Zoomyboy\LaravelNami\NamiUser;

class InitializeFees {

    private Api $api;
    
    public function __construct(Api $api) {
        $this->api = $api;
    }

    public function handle(NamiUser $user): void
    {
        $this->api->feesOf($user->getNamiGroupId())->each(function($fee) {
            \App\Fee::create(['nami_id' => $fee->id, 'name' => $fee->name])
                ->subscriptions()->create([
                    'name' => $fee->name,
                    'amount' => 1000,
                ]);
        });
    }
}
