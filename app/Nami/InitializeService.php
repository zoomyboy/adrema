<?php

namespace App\Nami;

class InitializeService {

    public $api;

    public function __construct(Api $api) {
        $this->api = $api;
    }

    public function handle() {
        dd(auth()->user()->getNamiService()->fees());
    }

}
