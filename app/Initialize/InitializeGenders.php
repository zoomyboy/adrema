<?php 

namespace App\Initialize;

use Zoomyboy\LaravelNami\Api;
use Zoomyboy\LaravelNami\NamiUser;

class InitializeGenders {

    private Api $api;

    public function __construct(Api $api) {
        $this->api = $api;
    }

    public function handle(NamiUser $user): void
    {
        $this->api->genders()->each(function($gender) {
            \App\Gender::create(['nami_id' => $gender->id, 'name' => $gender->name]);
        });
    }
}
