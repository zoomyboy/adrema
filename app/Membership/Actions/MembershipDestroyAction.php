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

    public function handle(Membership $membership): void
    {
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
        $this->startJob($membership);

        return response()->json([]);
    }

    /**
     * @param mixed $parameters
     */
    public function jobState(WithJobState $jobState, ...$parameters): WithJobState
    {
        $member = $parameters[0]->member;

        return $jobState
            ->before('Mitgliedschaft für ' . $member->fullname . ' wird gelöscht')
            ->after('Mitgliedschaft für ' . $member->fullname . ' gelöscht')
            ->failed('Fehler beim Löschen der Mitgliedschaft für ' . $member->fullname)
            ->shouldReload(JobChannels::make()->add('member')->add('membership'));
    }
}
