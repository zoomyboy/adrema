<?php 

namespace App\Initialize;

class InitializeCountries {

    private $bar;
    private $api;

    public function __construct($bar, $api) {
        $this->bar = $bar;
        $this->api = $api;
    }

    public function handle() {
        $this->bar->task('Synchronisiere Länder', function() {
            $this->api->countries()->each(function($country) {
                \App\Country::create(['nami_id' => $country->id, 'name' => $country->name]);
            });
        });
    }
}
