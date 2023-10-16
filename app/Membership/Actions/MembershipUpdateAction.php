<?php

namespace App\Membership\Actions;

use App\Activity;
use App\Lib\JobMiddleware\JobChannels;
use App\Lib\JobMiddleware\WithJobState;
use App\Lib\Queue\TracksJob;
use App\Maildispatcher\Actions\ResyncAction;
use App\Member\Membership;
use App\Subactivity;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\In;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class MembershipUpdateAction
{
    use AsAction;
    use TracksJob;

    public function handle(Membership $membership, Activity $activity, ?Subactivity $subactivity, ?Carbon $promisedAt): Membership
    {
        $membership->update([
            'activity_id' => $activity->id,
            'subactivity_id' => $subactivity ? $subactivity->id : null,
            'promised_at' => $promisedAt,
        ]);

        ResyncAction::dispatch();

        return $membership;
    }

    /**
     * @return array<string, array<int, string|In>>
     */
    public function rules(): array
    {
        $subactivityRule = request()->activity_id ? ['nullable', Rule::exists('activity_subactivity', 'subactivity_id')->where('activity_id', request()->activity_id)] : ['nullable'];

        return [
            'activity_id' => ['bail', 'required', 'exists:activities,id'],
            'subactivity_id' => $subactivityRule,
            'promised_at' => ['nullable', 'date'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function getValidationAttributes(): array
    {
        return [
            'activity_id' => 'Tätigkeit',
            'subactivity_id' => 'Untertätigkeit',
        ];
    }

    public function asController(Membership $membership, ActionRequest $request): JsonResponse
    {
        $this->startJob(
            $membership,
            Activity::find($request->activity_id),
            $request->subactivity_id ? Subactivity::find($request->subactivity_id) : null,
            $request->promised_at ? Carbon::parse($request->promised_at) : null,
        );

        return response()->json([]);
    }

    /**
     * @param mixed $parameters
     */
    public function jobState(WithJobState $jobState, ...$parameters): WithJobState
    {
        $member = $parameters[0]->member;

        return $jobState
            ->before('Mitgliedschaft für ' . $member->fullname . ' wird aktualisiert')
            ->after('Mitgliedschaft für ' . $member->fullname . ' aktualisiert')
            ->failed('Fehler beim Aktualisieren der Mitgliedschaft für ' . $member->fullname)
            ->shouldReload(JobChannels::make()->add('member')->add('membership'));
    }
}
