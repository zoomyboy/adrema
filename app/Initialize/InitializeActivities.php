<?php 

namespace App\Initialize;

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

    public function handle() {
        $this->bar->task('Synchronisiere Tätigkeiten', function() {
            $this->api->group(auth()->user()->getNamiGroupId())->activities()->each(function($activity) {
                $activity =  \App\Activity::create([
                    'nami_id' => $activity->id,
                    'name' => $activity->name,
                    'is_try' => in_array($group->name, $this->tries),
                    'is_member' => in_array($group->name, $this->members),
                ]);


                $groups = [];
                $this->api->subactivitiesOf($activity->id)->each(function($group) use ($activity, &$groups) {
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
