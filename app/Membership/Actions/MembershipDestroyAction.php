<?php

namespace App\Membership\Actions;

use App\Lib\JobMiddleware\JobChannels;
use App\Lib\JobMiddleware\WithJobState;
use App\Lib\Queue\TracksJob;
use App\Maildispatcher\Actions\ResyncAction;
use App\Member\Membership;
use App\Setting\NamiSettings;
use Illuminate\Http\JsonResponse;
use Lorisleiva\Actions\Concerns\AsAction;

class MembershipDestroyAction
{
    use AsAction;
    use TracksJob;

    public function handle(int $membershipId): void
    {
        $membership = Membership::find($membershipId);
        $api = app(NamiSettings::class)->login();

        if ($membership->hasNami) {
            $api->deleteMembership(
                $membership->member->nami_id,
                $api->membership($membership->member->nami_id, $membership->nami_id)
            );
        }

        $membership->delete();

        if ($membership->hasNami) {
            $membership->member->syncVersion();
        }

        ResyncAction::dispatch();
    }

    public function asController(Membership $membership): JsonResponse
    {
        $this->startJob($membership->id, $membership->member->fullname);

        return response()->json([]);
    }

    /**
     * @param mixed $parameters
     */
    public function jobState(WithJobState $jobState, ...$parameters): WithJobState
    {
        $memberName = $parameters[1];

        return $jobState
            ->before('Mitgliedschaft für ' . $memberName . ' wird gelöscht')
            ->after('Mitgliedschaft für ' . $memberName . ' gelöscht')
            ->failed('Fehler beim Löschen der Mitgliedschaft für ' . $memberName)
            ->shouldReload(JobChannels::make()->add('member')->add('membership'));
    }
}
