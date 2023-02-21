<?php

namespace App\Membership\Actions;

use App\Activity;
use App\Group;
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
use Zoomyboy\LaravelNami\Exceptions\HttpException;

class MembershipStoreAction
{
    use AsAction;

    public function handle(Member $member, Activity $activity, ?Subactivity $subactivity, Group $group, ?Carbon $promisedAt, NamiSettings $settings): Membership
    {
        $from = now()->startOfDay();

        $subactivity = $subactivity ?: new Subactivity(['nami_id' => null, 'id' => null]);

        if ($activity->hasNami && ($subactivity->id === null || $subactivity->hasNami)) {
            try {
                $namiId = $settings->login()->putMembership($member->nami_id, NamiMembership::from([
                    'startsAt' => $from,
                    'groupId' => $group->nami_id,
                    'activityId' => $activity->nami_id,
                    'subactivityId' => $subactivity->nami_id,
                ]));
            } catch (HttpException $e) {
                throw ValidationException::withMessages(['nami' => htmlspecialchars($e->getMessage())]);
            }
        }

        $membership = $member->memberships()->create([
            'activity_id' => $activity->id,
            'subactivity_id' => $subactivity->id,
            'promised_at' => $promisedAt,
            'group_id' => $group->id,
            'from' => $from,
            'nami_id' => $namiId ?? null,
        ]);

        if ($activity->hasNami && ($subactivity->id === null || $subactivity->hasNami)) {
            $member->syncVersion();
        }

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
            Group::findOrFail($request->input('group_id', -1)),
            $request->promised_at ? Carbon::parse($request->promised_at) : null,
            $settings,
        );

        return redirect()->back();
    }
}
