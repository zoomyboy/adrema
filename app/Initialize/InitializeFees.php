<?php 

namespace App\Initialize;

class InitializeFees {

    private $bar;
    private $api;
    
    public function __construct($bar, $api) {
        $this->bar = $bar;
        $this->api = $api;
    }

    public function handle() {
        $this->bar->task('Synchronisiere Beiträge', function() {
            $this->api->group(auth()->user()->getNamiGroupId())->fees()->each(function($fee) {
                \App\Fee::create(['nami_id' => $fee->id, 'name' => $fee->name]);
            });
        });
    }
}
