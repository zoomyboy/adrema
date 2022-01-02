<?php

namespace App\Initialize;

class ActivityCreator {

    private array $tries = [
        'Schnuppermitgliedschaft',
    ];

    private array $members = [
        '€ Mitglied',
        'Schnuppermitgliedschaft',
    ];
    

    public function createFor($api, int $groupId) {
        $api->activities($groupId)->each(function($activity) use ($api) {
            $activity =  \App\Activity::updateOrCreate(['nami_id' => $activity->id], [
                'nami_id' => $activity->id,
                'name' => $activity->name,
                'is_try' => in_array($activity->name, $this->tries),
                'is_member' => in_array($activity->name, $this->members),
            ]);

            $groups = [];
            $api->subactivitiesOf($activity->nami_id)->each(function($group) use (&$groups) {
                $group = \App\Subactivity::updateOrCreate(['nami_id' => $group->id], [
                    'nami_id' => $group->id,
                    'name' => $group->name,
                ]);
                $groups[] = $group->id;
            });
            $activity->subactivities()->sync($groups);
        });
    }

}

