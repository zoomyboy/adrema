<?php

namespace App\Actions;

use App\Course\Models\Course;
use App\Member\Member;
use App\Setting\NamiSettings;
use Lorisleiva\Actions\Concerns\AsAction;
use Zoomyboy\LaravelNami\Api;

class PullCoursesAction
{
    use AsAction;

    public function handle(Member $member): void
    {
        if (!$member->hasNami) {
            return;
        }

        $courses = $this->api()->coursesFor($member->nami_id);

        foreach ($courses as $course) {
            $member->courses()->updateOrCreate(['nami_id' => $course->id], [
                'course_id' => Course::nami($course->courseId)->id,
                'organizer' => $course->organizer,
                'nami_id' => $course->id,
                'completed_at' => $course->completedAt,
                'event_name' => $course->eventName,
            ]);
        }

        $courseIds = $courses->map(fn ($course) => $course->id)->toArray();
        $member->courses()->whereNotIn('nami_id', $courseIds)->whereNotNull('nami_id')->delete();
    }

    private function api(): Api
    {
        return app(NamiSettings::class)->login();
    }
}
