<?php

namespace App\Initialize;

use Zoomyboy\LaravelNami\Api;
use Zoomyboy\LaravelNami\NamiUser;

class InitializeActivities {

    private Api $api;

    public function __construct(Api $api) {
        $this->api = $api;
    }

    public function handle(NamiUser $user): void
    {
        app(ActivityCreator::class)->createFor($this->api, $user->getNamiGroupId());
    }
}
