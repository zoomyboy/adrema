<?php 

namespace App\Initialize;

class InitializeConfessions {

    private $bar;
    private $api;
    public $nullName = 'ohne Konfession';

    public function __construct($bar, $api) {
        $this->bar = $bar;
        $this->api = $api;
    }

    public function handle() {
        $this->bar->task('Synchronisiere Konfessionen', function() {
            collect($this->api->confessions()->data)->each(function($confession) {
                \App\Confession::create(['nami_id' => $confession->id, 'name' => $confession->descriptor, 'is_null' => $this->nullName === $confession->descriptor]);
            });
        });
    }
}
