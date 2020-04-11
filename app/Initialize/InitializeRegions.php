<?php 

namespace App\Initialize;

class InitializeRegions {

    private $bar;
    private $api;
    private $nullName = 'Nicht-DE (Ausland)';
    
    public function __construct($bar, $api) {
        $this->bar = $bar;
        $this->api = $api;
    }

    public function handle() {
        $this->bar->task('Synchronisiere Bundesländer', function() {
            collect($this->api->regions()->data)->each(function($region) {
                \App\Region::create(['nami_id' => $region->id, 'name' => $region->descriptor, 'is_null' => $region->descriptor === $this->nullName]);
            });
        });
    }
}
