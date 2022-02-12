<?php 

namespace App\Initialize;

use Zoomyboy\LaravelNami\Api;

class InitializeRegions {

    private Api $api;
    private string $nullName = 'Nicht-DE';
    
    public function __construct(Api $api) {
        $this->api = $api;
    }

    public function handle(): void
    {
        $this->api->regions()->each(function($region) {
            \App\Region::create(['nami_id' => $region->id, 'name' => $region->name, 'is_null' => $region->name == $this->nullName]);
        });
    }
}
