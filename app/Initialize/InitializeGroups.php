<?php

namespace App\Initialize;

use App\Group;
use DB;
use Zoomyboy\LaravelNami\Api;

class InitializeGroups
{
    private Api $api;

    public function __construct(Api $api)
    {
        $this->api = $api;
    }

    public function handle(): void
    {
        $this->api->groups()->each(function ($group) {
            $parent = Group::updateOrCreate(['nami_id' => $group->id], ['nami_id' => $group->id, 'name' => $group->name]);

            $this->syncChildren($group->id, $parent);
        });
    }

    private function syncChildren(int $groupId, Group $parent): void
    {
        $this->api->subgroupsOf($groupId)->each(function ($subgroup) use ($parent) {
            $newParent = Group::updateOrCreate(['nami_id' => $subgroup->id], ['nami_id' => $subgroup->id, 'name' => $subgroup->name, 'parent_id' => $parent->id]);
            $this->syncChildren($subgroup->id, $newParent);
        });
    }

    public function restore(): void
    {
        DB::table('groups')->delete();
    }
}
