<?php

namespace App\Course\Actions;

use App\Course\Models\CourseMember;
use App\Lib\JobMiddleware\JobChannels;
use App\Lib\JobMiddleware\WithJobState;
use App\Lib\Queue\TracksJob;
use App\Setting\NamiSettings;
use Illuminate\Http\JsonResponse;
use Lorisleiva\Actions\Concerns\AsAction;

class CourseDestroyAction
{
    use AsAction;
    use TracksJob;

    public function handle(int $courseId): void
    {
        $course = CourseMember::find($courseId);
        app(NamiSettings::class)->login()->deleteCourse($course->member->nami_id, $course->nami_id);

        $course->delete();
    }

    public function asController(CourseMember $course): JsonResponse
    {
        $this->startJob($course->id, $course->member->fullname);

        return response()->json([]);
    }

    /**
     * @param mixed $parameters
     */
    public function jobState(WithJobState $jobState, ...$parameters): WithJobState
    {
        $memberFullname = $parameters[1];

        return $jobState
            ->before('Ausbildung für ' . $memberFullname . ' wird gelöscht')
            ->after('Ausbildung für ' . $memberFullname . ' gelöscht')
            ->failed('Fehler beim Löschen der Ausbildung für ' . $memberFullname)
            ->shouldReload(JobChannels::make()->add('member')->add('course'));
    }
}
