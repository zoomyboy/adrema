<?php 

namespace App\Initialize;

use Zoomyboy\LaravelNami\NamiUser;

class InitializeGenders {

    private $bar;
    private $api;

    public function __construct($bar, $api) {
        $this->bar = $bar;
        $this->api = $api;
    }

    public function handle(NamiUser $user) {
        $this->bar->task('Synchronisiere Geschlechter', function() {
            $this->api->genders()->each(function($gender) {
                \App\Gender::create(['nami_id' => $gender->id, 'name' => $gender->name]);
            });
        });
    }
}
