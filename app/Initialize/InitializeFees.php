<?php 

namespace App\Initialize;

use Zoomyboy\LaravelNami\NamiUser;

class InitializeFees {

    private $bar;
    private $api;
    
    public function __construct($bar, $api) {
        $this->bar = $bar;
        $this->api = $api;
    }

    public function handle(NamiUser $user) {
        $this->bar->task('Synchronisiere Beiträge', function() use ($user) {
            $this->api->feesOf($user->getNamiGroupId())->each(function($fee) {
                \App\Fee::create(['nami_id' => $fee->id, 'name' => $fee->name])
                    ->subscriptions()->create([
                        'name' => $fee->name,
                        'amount' => 1000,
                    ]);
            });
        });
    }
}
