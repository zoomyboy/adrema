<?php 

namespace App\Initialize;

class InitializeFees {

    private $bar;
    private $api;
    
    public function __construct($bar, $api) {
        $this->bar = $bar;
        $this->api = $api;
    }

    public function handle() {
        $this->bar->task('Synchronisiere Beiträge', function() {
            collect($this->api->fees()->data)->each(function($fee) {
                $title = preg_replace('/^.*\((.*)\).*$/', '\\1', $fee->descriptor);
                \App\Fee::create(['nami_id' => $fee->id, 'title' => $title]);
            });
        });
    }
}
