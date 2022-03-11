<?php

namespace App\Initialize;

use App\Fee;
use Zoomyboy\LaravelNami\Api;

class InitializeFees
{
    private Api $api;

    public function __construct(Api $api)
    {
        $this->api = $api;
    }

    public function handle(): void
    {
        $group = $this->api->groups()->first()->id;
        $this->api->feesOf($group)->each(function ($fee) {
            Fee::create(['nami_id' => $fee->id, 'name' => $fee->name])
                ->subscriptions()->create([
                    'name' => $fee->name,
                    'amount' => 1000,
                ]);
        });
    }
}
