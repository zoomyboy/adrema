<?php

namespace App\Initialize;

use DB;
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
        $groupId = $this->api->groups()->first()->id;
        app(ActivityCreator::class)->createFor($this->api, $groupId);
    }

    public function restore(): void
    {
        DB::table('activity_subactivity')->delete();
        DB::table('activities')->delete();
        DB::table('subactivities')->delete();
    }
}
