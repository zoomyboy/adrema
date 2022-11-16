<?php

namespace App\Initialize;

use Zoomyboy\LaravelNami\Api;

class ActivityCreator
{
    /** @var array<int, string> */
    private array $tries = [
        'Schnuppermitgliedschaft',
    ];

    /** @var array<int, string> */
    private array $members = [
        '€ Mitglied',
        'Schnuppermitgliedschaft',
    ];

    /** @var array<int, string> */
    private array $filterableActivities = [
        '€ Mitglied',
        '€ passive Mitgliedschaft',
        '€ KassiererIn',
        '€ LeiterIn',
        'Schnuppermitgliedschaft',
    ];

    /** @var array<int, string> */
    private array $filterableSubactivities = [
        'Biber',
        'Wölfling',
        'Jungpfadfinder',
        'Pfadfinder',
        'Vorstand',
        'Rover',
    ];

    /** @var array<int, string> */
    private array $ageGroups = [
        'Biber',
        'Wölfling',
        'Jungpfadfinder',
        'Pfadfinder',
        'Rover',
    ];

    /** @var array<int, string> */
    private array $efz = [
        '€ LeiterIn',
    ];

    public function createFor(Api $api, int $groupId): void
    {
        $api->activities($groupId)->each(function ($activity) use ($api) {
            $activity = \App\Activity::updateOrCreate(['nami_id' => $activity->id], [
                'nami_id' => $activity->id,
                'name' => $activity->name,
                'is_try' => in_array($activity->name, $this->tries),
                'is_member' => in_array($activity->name, $this->members),
                'is_filterable' => in_array($activity->name, $this->filterableActivities),
                'has_efz' => in_array($activity->name, $this->efz),
            ]);

            $groups = [];
            $api->subactivitiesOf($activity->nami_id)->each(function ($group) use (&$groups) {
                $group = \App\Subactivity::updateOrCreate(['nami_id' => $group->id], [
                    'nami_id' => $group->id,
                    'name' => $group->name,
                    'is_filterable' => in_array($group->name, $this->filterableSubactivities),
                    'is_age_group' => in_array($group->name, $this->ageGroups),
                ]);
                $groups[] = $group->id;
            });
            $activity->subactivities()->sync($groups);
        });
    }
}
