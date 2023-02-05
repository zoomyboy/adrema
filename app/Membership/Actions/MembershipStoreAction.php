<?php

namespace App\Membership\Actions;

use App\Activity;
use App\Member\Member;
use App\Member\Membership;
use App\Setting\NamiSettings;
use App\Subactivity;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\In;
use Illuminate\Validation\ValidationException;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Zoomyboy\LaravelNami\Data\Membership as NamiMembership;
use Zoomyboy\LaravelNami\NamiException;

class MembershipStoreAction
{
    use AsAction;

    public function handle(Member $member, Activity $activity, ?Subactivity $subactivity, ?Carbon $promisedAt, NamiSettings $settings): Membership
    {
        $from = now()->startOfDay();

        try {
            $namiId = $settings->login()->putMembership($member->nami_id, NamiMembership::from([
                'startsAt' => $from,
                'groupId' => $member->group->nami_id,
                'activityId' => $activity->nami_id,
                'subactivityId' => $subactivity ? $subactivity->nami_id : null,
            ]));
        } catch (NamiException $e) {
            throw ValidationException::withMessages(['nami' => htmlspecialchars($e->getMessage())]);
        }

        $membership = $member->memberships()->create([
            'activity_id' => $activity->id,
            'subactivity_id' => $subactivity ? $subactivity->id : null,
            'promised_at' => $promisedAt,
            ...['nami_id' => $namiId, 'group_id' => $member->group->id, 'from' => $from],
        ]);

        $member->syncVersion();

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

    public function asController(Member $member, ActionRequest $request, NamiSettings $settings): RedirectResponse
    {
        $this->handle(
            $member,
            Activity::find($request->activity_id),
            $request->subactivity_id ? Subactivity::find($request->subactivity_id) : null,
            $request->promised_at ? Carbon::parse($request->promised_at) : null,
            $settings,
        );

        return redirect()->back();
    }
}
