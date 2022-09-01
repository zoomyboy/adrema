<?php

namespace App\Initialize;

use DB;
use Zoomyboy\LaravelNami\Api;

class InitializeGenders
{
    private Api $api;

    public function __construct(Api $api)
    {
        $this->api = $api;
    }

    public function handle(): void
    {
        $this->api->genders()->each(function ($gender) {
            \App\Gender::create(['nami_id' => $gender->id, 'name' => $gender->name]);
        });
    }

    public function restore(): void
    {
        DB::table('genders')->delete();
    }
}
