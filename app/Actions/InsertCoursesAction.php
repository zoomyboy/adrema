<?php

namespace App\Actions;

use App\Course\Models\Course;
use App\Member\Member;
use Illuminate\Support\Collection;
use Lorisleiva\Actions\Concerns\AsAction;
use Zoomyboy\LaravelNami\Data\Course as NamiCourse;

class InsertCoursesAction
{
    use AsAction;

    /**
     * @param Collection<int, NamiCourse> $courses
     */
    public function handle(Member $member, Collection $courses): void
    {
        if (!$member->hasNami) {
            return;
        }

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
}
