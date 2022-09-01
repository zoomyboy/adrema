<?php

namespace App\Initialize;

use App\Course\Models\Course;
use DB;
use Zoomyboy\LaravelNami\Api;

class InitializeCourses
{
    private Api $api;

    public function __construct(Api $api)
    {
        $this->api = $api;
    }

    public function handle(): void
    {
        $this->api->courses()->each(function ($course) {
            Course::create(['nami_id' => $course->id, 'name' => $course->name]);
        });
    }

    public function restore(): void
    {
        DB::table('courses')->delete();
    }
}
