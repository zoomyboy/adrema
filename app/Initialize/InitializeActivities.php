<?php

namespace App\Initialize;

use Zoomyboy\LaravelNami\NamiUser;

class InitializeActivities {

    private $bar;
    private $api;

    private array $tries = [
        'Schnuppermitgliedschaft',
    ];

    private array $members = [
        '€ Mitglied',
        'Schnuppermitgliedschaft',
    ];
    
    public function __construct($bar, $api) {
        $this->bar = $bar;
        $this->api = $api;
    }

    public function handle(NamiUser $user) {
        $this->bar->task('Synchronisiere Tätigkeiten', function() use ($user) {
            $this->api->activities($user->getNamiGroupId())->each(function($activity) {
                $activity =  \App\Activity::create([
                    'nami_id' => $activity->id,
                    'name' => $activity->name,
                    'is_try' => in_array($activity->name, $this->tries),
                    'is_member' => in_array($activity->name, $this->members),
                ]);


                $groups = [];
                $this->api->subactivitiesOf($activity->nami_id)->each(function($group) use (&$groups) {
                    $group = \App\Subactivity::updateOrCreate(['nami_id' => $group->id], [
                        'nami_id' => $group->id,
                        'name' => $group->name,
                    ]);
                    $groups[] = $group->id;
                });
                $activity->subactivities()->sync($groups);
            });
        });
    }
}
