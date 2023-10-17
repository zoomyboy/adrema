<?php

namespace App\Course\Actions;

use App\Course\Models\Course;
use App\Lib\JobMiddleware\JobChannels;
use App\Lib\JobMiddleware\WithJobState;
use App\Lib\Queue\TracksJob;
use App\Member\Member;
use App\Setting\NamiSettings;
use Illuminate\Http\JsonResponse;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class CourseStoreAction
{
    use AsAction;
    use TracksJob;

    /**
     * @param array<string, mixed> $attributes
     */
    public function handle(Member $member, array $attributes): void
    {
        $course = Course::where('id', $attributes['course_id'])->firstOrFail();

        $payload = collect($attributes)->only(['event_name', 'completed_at', 'organizer'])->merge([
            'course_id' => $course->nami_id,
        ])->toArray();

        $namiId = app(NamiSettings::class)->login()->createCourse($member->nami_id, $payload);

        $member->courses()->create([
            ...$attributes,
            'nami_id' => $namiId,
        ]);
    }

    /**
     * @return array<string, string>
     */
    public function rules(): array
    {
        return [
            'organizer' => 'required|max:255',
            'event_name' => 'required|max:255',
            'completed_at' => 'required|date',
            'course_id' => 'required|exists:courses,id',
        ];
    }

    public function asController(Member $member, ActionRequest $request): JsonResponse
    {
        $this->startJob($member, $request->validated());

        return response()->json([]);
    }

    /**
     * @param mixed $parameters
     */
    public function jobState(WithJobState $jobState, ...$parameters): WithJobState
    {
        $member = $parameters[0];

        return $jobState
            ->before('Ausbildung für ' . $member->fullname . ' wird gespeichert')
            ->after('Ausbildung für ' . $member->fullname . ' gespeichert')
            ->failed('Fehler beim Erstellen der Ausbildung für ' . $member->fullname)
            ->shouldReload(JobChannels::make()->add('member')->add('course'));
    }
}
