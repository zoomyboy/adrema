<?php 

namespace App\Initialize;

class InitializeGenders {

    private $bar;
    private $api;
    public $nullName = 'keine Angabe';

    public function __construct($bar, $api) {
        $this->bar = $bar;
        $this->api = $api;
    }

    public function handle() {
        $this->bar->task('Synchronisiere Geschlechter', function() {
            collect($this->api->genders()->data)->each(function($gender) {
                \App\Gender::create(['nami_id' => $gender->id, 'name' => $gender->descriptor, 'is_null' => $gender->descriptor === $this->nullName]);
            });
        });
    }
}
