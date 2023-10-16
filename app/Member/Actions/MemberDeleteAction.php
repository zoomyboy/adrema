<?php

namespace App\Member\Actions;

use App\Lib\JobMiddleware\JobChannels;
use App\Lib\JobMiddleware\WithJobState;
use App\Lib\Queue\TracksJob;
use App\Maildispatcher\Actions\ResyncAction;
use App\Member\Member;
use Illuminate\Http\RedirectResponse;
use Lorisleiva\Actions\Concerns\AsAction;

class MemberDeleteAction
{

    use AsAction;
    use TracksJob;

    public function handle(int $memberId): void
    {
        $member = Member::findOrFail($memberId);

        if ($member->nami_id) {
            NamiDeleteMemberAction::run($member->nami_id);
        }

        $member->delete();
        ResyncAction::run();
    }

    public function asController(Member $member): RedirectResponse
    {
        $this->startJob($member->id);

        return redirect()->back();
    }

    /**
     * @param mixed $parameters
     */
    public function jobState(WithJobState $jobState, ...$parameters): WithJobState
    {
        $member = Member::findOrFail($parameters[0]);

        return $jobState
            ->before('Mitglied ' . $member->fullname . ' wird gelöscht')
            ->after('Mitglied ' . $member->fullname . ' gelöscht')
            ->failed('Löschen von ' . $member->fullname . ' fehlgeschlagen.')
            ->shouldReload(JobChannels::make()->add('member'));
    }
}
