<?php 

namespace App\Initialize;

class InitializeNationalities {

    private $api;

    public function __construct($api) {
        $this->api = $api;
    }

    public function handle() {
        $this->api->nationalities()->each(function($nationality) {
            \App\Nationality::create(['nami_id' => $nationality->id, 'name' => $nationality->name]);
        });
    }
}
