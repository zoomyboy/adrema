<?php

namespace App\Initialize;

class InitializeCountries {

    private $api;

    public function __construct($api) {
        $this->api = $api;
    }

    public function handle() {
        $this->api->countries()->each(function($country) {
            \App\Country::create(['nami_id' => $country->id, 'name' => $country->name]);
        });
    }
}
