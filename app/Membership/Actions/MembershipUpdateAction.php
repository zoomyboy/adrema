<?php

namespace App\Membership\Actions;

use App\Activity;
use App\Member\Member;
use App\Member\Membership;
use App\Subactivity;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\In;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class MembershipUpdateAction
{
    use AsAction;

    public function handle(Membership $membership, Activity $activity, ?Subactivity $subactivity, ?Carbon $promisedAt): Membership
    {
        $from = now()->startOfDay();

        $membership->update([
            'activity_id' => $activity->id,
            'subactivity_id' => $subactivity ? $subactivity->id : null,
            'promised_at' => $promisedAt,
        ]);

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

    public function asController(Member $member, Membership $membership, ActionRequest $request): RedirectResponse
    {
        $this->handle(
            $membership,
            Activity::find($request->activity_id),
            $request->subactivity_id ? Subactivity::find($request->subactivity_id) : null,
            $request->promised_at ? Carbon::parse($request->promised_at) : null,
        );

        return redirect()->back();
    }
}
