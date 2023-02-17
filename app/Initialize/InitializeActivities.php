<?php

namespace App\Initialize;

use Illuminate\Support\Facades\DB;
use Zoomyboy\LaravelNami\Api;

class InitializeActivities
{
    private Api $api;

    public function __construct(Api $api)
    {
        $this->api = $api;
    }

    public function handle(): void
    {
        app(ActivityCreator::class)->createFor($this->api, $this->api->groups()->first());
    }

    public function restore(): void
    {
        DB::table('activity_subactivity')->delete();
        DB::table('activities')->delete();
        DB::table('subactivities')->delete();
    }
}
