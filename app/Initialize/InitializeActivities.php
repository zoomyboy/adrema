<?php 

namespace App\Initialize;

class InitializeActivities {

    private $bar;
    private $api;
    
    public function __construct($bar, $api) {
        $this->bar = $bar;
        $this->api = $api;
    }

    public function handle() {
        $this->bar->task('Synchronisiere Tätigkeiten', function() {
            $this->api->group(auth()->user()->getNamiGroupId())->activities()->each(function($activity) {
                $activity =  \App\Activity::create(['nami_id' => $activity->id, 'name' => $activity->name]);

                $groups = [];
                $this->api->subactivitiesOf($activity->id)->each(function($group) use ($activity, &$groups) {
                    $group = \App\Subactivity::updateOrCreate(['nami_id' => $group->id], ['nami_id' => $group->id, 'name' => $group->name]);
                    $groups[] = $group->id;
                });
                $activity->groups()->sync($groups);
            });
        });
    }
}
