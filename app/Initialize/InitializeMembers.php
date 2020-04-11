<?php 

namespace App\Initialize;

class InitializeMembers {

    private $bar;
    private $api;
    
    public function __construct($bar, $api) {
        $this->bar = $bar;
        $this->api = $api;
    }

    public function handle() {
        $allMembers = collect([]);

        $this->bar->task('Finde Mitglieder', function() use (&$allMembers) {
            $allMembers = collect($this->api->allMembers()->data);
        });

        $this->bar->tasks($allMembers, function($member) {
            return "Synchronisiere {$member->entries_vorname} {$member->entries_nachname}";
        }, function($member) {
            // Member::createFromNami($this->api->getMember($member->id)->data);
        });
    }
}
