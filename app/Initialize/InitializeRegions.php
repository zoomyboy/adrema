<?php 

namespace App\Initialize;

class InitializeRegions {

    private $bar;
    private $api;
    private $nullName = 'Nicht-DE';
    
    public function __construct($bar, $api) {
        $this->bar = $bar;
        $this->api = $api;
    }

    public function handle() {
        $this->bar->task('Synchronisiere Bundesländer', function() {
            $this->api->regions()->each(function($region) {
                \App\Region::create(['nami_id' => $region->id, 'name' => $region->name, 'is_null' => $region->name == $this->nullName]);
            });
        });
    }
}
