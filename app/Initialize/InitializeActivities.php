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
            collect($this->api->activities()->data)->each(function($activity) {
                $activity =  \App\Activity::create(['nami_id' => $activity->id, 'name' => $activity->descriptor]);

                $groups = [];
                collect($this->api->groupForActivity($activity->id)->data)->each(function($group) use ($activity, &$groups) {
                    $group = \App\Group::updateOrCreate(['nami_id' => $group->id], ['nami_id' => $group->id, 'name' => $group->descriptor]);
                    $groups[] = $group->id;
                });
                $activity->groups()->sync($groups);
            });
        });
    }
}
