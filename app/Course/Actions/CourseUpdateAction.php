<?php

namespace App\Course\Actions;

use App\Course\Models\Course;
use App\Course\Models\CourseMember;
use App\Course\Resources\CourseMemberResource;
use App\Lib\JobMiddleware\JobChannels;
use App\Lib\JobMiddleware\WithJobState;
use App\Lib\Queue\TracksJob;
use App\Member\Member;
use App\Setting\NamiSettings;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class CourseUpdateAction
{
    use AsAction;
    use TracksJob;

    /**
     * @return Collection<int, Course>
     */
    public function handle(CourseMember $course, array $attributes): void
    {
        app(NamiSettings::class)->login()->updateCourse(
            $course->member->nami_id,
            $course->nami_id,
            [
                ...$attributes,
                'course_id' => Course::find($attributes['course_id'])->nami_id,
            ]
        );

        $course->update($attributes);
    }

    /**
     * @return array<string, string>
     */
    public function rules()
    {
        return [
            'organizer' => 'required|max:255',
            'event_name' => 'required|max:255',
            'completed_at' => 'required|date',
            'course_id' => 'required|exists:courses,id',
        ];
    }

    public function asController(CourseMember $course, ActionRequest $request): JsonResponse
    {
        $this->startJob($course, $request->validated());

        return response()->json([]);
    }

    /**
     * @param mixed $parameters
     */
    public function jobState(WithJobState $jobState, ...$parameters): WithJobState
    {
        $member = $parameters[0]->member;

        return $jobState
            ->before('Ausbildung für ' . $member->fullname . ' wird gespeichert')
            ->after('Ausbildung für ' . $member->fullname . ' gespeichert')
            ->failed('Fehler beim Erstellen der Ausbildung für ' . $member->fullname)
            ->shouldReload(JobChannels::make()->add('member')->add('course'));
    }
}
