<?php

namespace App\Initialize;

use App\Confession;
use Zoomyboy\LaravelNami\Api;

class InitializeConfessions
{
    private Api $api;
    public string $nullName = 'ohne Konfession';

    public function __construct(Api $api)
    {
        $this->api = $api;
    }

    public function handle(): void
    {
        $this->api->confessions()->each(function ($confession) {
            Confession::create(['nami_id' => $confession->id, 'name' => $confession->name, 'is_null' => $this->nullName === $confession->name]);
        });
    }
}
