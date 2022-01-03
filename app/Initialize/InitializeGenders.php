<?php 

namespace App\Initialize;

use Zoomyboy\LaravelNami\NamiUser;

class InitializeGenders {

    private $api;

    public function __construct($api) {
        $this->api = $api;
    }

    public function handle(NamiUser $user) {
        $this->api->genders()->each(function($gender) {
            \App\Gender::create(['nami_id' => $gender->id, 'name' => $gender->name]);
        });
    }
}
