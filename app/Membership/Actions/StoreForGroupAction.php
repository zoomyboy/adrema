<?php

namespace App\Membership\Actions;

use App\Activity;
use App\Group;
use App\Lib\JobMiddleware\WithJobState;
use App\Lib\Queue\TracksJob;
use App\Maildispatcher\Actions\ResyncAction;
use App\Member\Member;
use App\Member\Membership;
use App\Setting\NamiSettings;
use App\Subactivity;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class StoreForGroupAction
{
    use AsAction;
    use TracksJob;

    /**
     * @return array<string, string>
     */
    public function rules(): array
    {
        return [
            'group_id' => 'required|numeric|exists:groups,id',
            'activity_id' => 'required|numeric|exists:activities,id',
            'subactivity_id' => 'required|numeric|exists:subactivities,id',
            'members' => 'array',
            'members.*' => 'numeric|exists:members,id',
        ];
    }

    /**
     * @param array<int, int> $members
     */
    public function handle(Group $group, Activity $activity, Subactivity $subactivity, array $members): void
    {
        DB::transaction(function () use ($activity, $subactivity, $group, $members) {
            $attributes = [
                'group_id' => $group->id,
                'activity_id' => $activity->id,
                'subactivity_id' => $subactivity->id,
            ];

            Membership::where($attributes)->active()->whereNotIn('member_id', $members)->get()
                ->each(fn ($membership) => MembershipDestroyAction::run($membership->member, $membership, app(NamiSettings::class)));

            collect($members)
                ->except(Membership::where($attributes)->active()->pluck('member_id'))
                ->map(fn ($memberId) => Member::findOrFail($memberId))
                ->each(fn ($member) => MembershipStoreAction::run(
                    $member,
                    $activity,
                    $subactivity,
                    $group,
                    null,
                    app(NamiSettings::class),
                ));


            ResyncAction::dispatch();
        });
    }

    public function asController(ActionRequest $request): void
    {
        /**
         * @var array{members: array<int, int>, group_id: int, activity_id: int, subactivity_id: int}
         */
        $input = $request->validated();

        $this->startJob(
            Group::findOrFail($input['group_id']),
            Activity::findOrFail($input['activity_id']),
            Subactivity::findOrFail($input['subactivity_id']),
            $input['members'],
        );
    }

    /**
     * @param mixed $parameters
     */
    public function jobState(WithJobState $jobState, ...$parameters): WithJobState
    {
        return $jobState
            ->before('Gruppen werden aktualisiert')
            ->after('Gruppen aktualisiert')
            ->failed('Aktualisieren von Gruppen fehlgeschlagen');
    }

    public function jobChannel(): string
    {
        return 'group';
    }
}
