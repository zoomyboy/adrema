<?php

namespace App\Initialize;

use Zoomyboy\LaravelNami\NamiUser;

class InitializeActivities {

    private $bar;
    private $api;

    public function __construct($bar, $api) {
        $this->bar = $bar;
        $this->api = $api;
    }

    public function handle(NamiUser $user) {
        $this->bar->task('Synchronisiere Tätigkeiten', function() use ($user) {
            app(ActivityCreator::class)->createFor($this->api, $user->getNamiGroupId());
        });
    }
}
