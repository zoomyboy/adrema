<?php

namespace App\Initialize;

use App\Nationality;
use Zoomyboy\LaravelNami\Api;

class InitializeNationalities
{
    private Api $api;

    public function __construct(Api $api)
    {
        $this->api = $api;
    }

    public function handle(Api $api): void
    {
        $this->api->nationalities()->each(function ($nationality) {
            Nationality::create(['nami_id' => $nationality->id, 'name' => $nationality->name]);
        });
    }
}
