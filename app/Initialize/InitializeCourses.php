<?php 

namespace App\Initialize;

use App\Course\Models\Course;
use Aweos\Agnoster\Progress\Progress;
use Zoomyboy\LaravelNami\Api;
use Zoomyboy\LaravelNami\NamiUser;

class InitializeCourses {

    private Progress $bar;
    private Api $api;

    public function __construct(Progress $bar, Api $api) {
        $this->bar = $bar;
        $this->api = $api;
    }

    public function handle(NamiUser $user): void
    {
        $this->api->courses()->each(function($course) {
            Course::create(['nami_id' => $course->id, 'name' => $course->name]);
        });
    }
}
