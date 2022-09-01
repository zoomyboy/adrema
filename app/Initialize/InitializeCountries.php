<?php

namespace App\Initialize;

use DB;
use Zoomyboy\LaravelNami\Api;

class InitializeCountries
{
    private Api $api;

    public function __construct(Api $api)
    {
        $this->api = $api;
    }

    public function handle(): void
    {
        $this->api->countries()->each(function ($country) {
            \App\Country::create(['nami_id' => $country->id, 'name' => $country->name]);
        });
    }

    public function restore(): void
    {
        DB::table('countries')->delete();
    }
}
