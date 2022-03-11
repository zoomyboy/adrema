<?php

namespace App\Initialize;

use Zoomyboy\LaravelNami\Api;

class InitializeActivities
{
    private Api $api;

    public function __construct(Api $api)
    {
        $this->api = $api;
    }

    public function handle(): void
    {
        $groupId = $this->api->groups()->first()->id;
        app(ActivityCreator::class)->createFor($this->api, $groupId);
    }
}
