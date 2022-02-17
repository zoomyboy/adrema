<?php 

namespace App\Initialize;

use App\Group;
use Zoomyboy\LaravelNami\Api;
use Zoomyboy\LaravelNami\NamiUser;

class InitializeGroups {

    private Api $api;

    public function __construct(Api $api) {
        $this->api = $api;
    }

    public function handle(): void
    {
        $this->api->groups()->each(function($group) {
            $parent = Group::updateOrCreate(['nami_id' => $group->id], ['nami_id' => $group->id, 'name' => $group->name]);

            $this->api->subgroupsOf($group->id)->each(function ($subgroup) use ($parent) {
                Group::updateOrCreate(['nami_id' => $subgroup->id], ['nami_id' => $subgroup->id, 'name' => $subgroup->name, 'parent_id' => $parent->id]);
            });
        });
    }
}
