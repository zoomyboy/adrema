<?php

namespace App\Initialize;

use Zoomyboy\LaravelNami\Api;

class InitializeNationalities {

    private Api $api;

    public function __construct(Api $api) {
        $this->api = $api;
    }

    public function handle(): void
    {
        $this->api->nationalities()->each(function($nationality) {
            \App\Nationality::create(['nami_id' => $nationality->id, 'name' => $nationality->name]);
        });
    }
}
