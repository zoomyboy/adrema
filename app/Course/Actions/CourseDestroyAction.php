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

    public function handle(CourseMember $course): void
    {
        app(NamiSettings::class)->login()->deleteCourse($course->member->nami_id, $course->nami_id);

        $course->delete();
    }

    public function asController(CourseMember $course): JsonResponse
    {
        $this->startJob($course);

        return response()->json([]);
    }

    /**
     * @param mixed $parameters
     */
    public function jobState(WithJobState $jobState, ...$parameters): WithJobState
    {
        $member = $parameters[0]->member;

        return $jobState
            ->before('Ausbildung für ' . $member->fullname . ' wird gelöscht')
            ->after('Ausbildung für ' . $member->fullname . ' gelöscht')
            ->failed('Fehler beim Löschen der Ausbildung für ' . $member->fullname)
            ->shouldReload(JobChannels::make()->add('member')->add('course'));
    }
}
