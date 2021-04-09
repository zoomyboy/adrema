<?php 

namespace App\Initialize;

class InitializeGenders {

    private $bar;
    private $api;
    public $nullName = 'Keine Angabe';

    public function __construct($bar, $api) {
        $this->bar = $bar;
        $this->api = $api;
    }

    public function handle() {
        $this->bar->task('Synchronisiere Geschlechter', function() {
            $this->api->genders()->each(function($gender) {
                \App\Gender::create(['nami_id' => $gender->id, 'name' => $gender->name, 'is_null' => $gender->name === $this->nullName]);
            });
        });
    }
}
