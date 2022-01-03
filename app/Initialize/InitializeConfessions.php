<?php 

namespace App\Initialize;

use App\Confession;

class InitializeConfessions {

    private $api;
    public $nullName = 'ohne Konfession';

    public function __construct($api) {
        $this->api = $api;
    }

    public function handle() {
        $this->api->confessions()->each(function($confession) {
            Confession::create(['nami_id' => $confession->id, 'name' => $confession->name, 'is_null' => $this->nullName === $confession->name]);
        });
    }
}
