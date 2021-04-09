<?php 

namespace App\Initialize;

class InitializeNationalities {

    private $bar;
    private $api;

    public function __construct($bar, $api) {
        $this->bar = $bar;
        $this->api = $api;
    }

    public function handle() {
        $this->bar->task('Synchronisiere Nationalitäten', function() {
            $this->api->group(auth()->user()->getNamiGroupId())->fees();
            $this->api->nationalities()->each(function($nationality) {
                \App\Nationality::create(['nami_id' => $nationality->id, 'name' => $nationality->name]);
            });
        });
    }
}
