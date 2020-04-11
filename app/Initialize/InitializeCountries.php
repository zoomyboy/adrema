<?php 

namespace App\Initialize;

class InitializeCountries {

    private $bar;
    private $api;

    public function __construct($bar, $api) {
        $this->bar = $bar;
        $this->api = $api;
    }

    public function handle() {
        $this->bar->task('Synchronisiere Länder', function() {
            collect($this->api->countries()->data)->each(function($nationality) {
                \App\Country::create(['nami_id' => $nationality->id, 'name' => $nationality->descriptor]);
            });
        });
    }
}
