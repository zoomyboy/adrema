<?php 

namespace App\Initialize;

class InitializeRegions {

    private $api;
    private $nullName = 'Nicht-DE';
    
    public function __construct($api) {
        $this->api = $api;
    }

    public function handle() {
        $this->api->regions()->each(function($region) {
            \App\Region::create(['nami_id' => $region->id, 'name' => $region->name, 'is_null' => $region->name == $this->nullName]);
        });
    }
}
