<?php

namespace App\Initialize;

use Zoomyboy\LaravelNami\NamiUser;

class InitializeActivities {

    private $api;

    public function __construct($api) {
        $this->api = $api;
    }

    public function handle(NamiUser $user) {
        app(ActivityCreator::class)->createFor($this->api, $user->getNamiGroupId());
    }
}
