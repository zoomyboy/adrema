<?php

namespace App\Member\Actions;

use App\Lib\JobMiddleware\WithJobState;
use App\Maildispatcher\Actions\ResyncAction;
use App\Member\Member;
use Illuminate\Http\RedirectResponse;
use Lorisleiva\Actions\Concerns\AsAction;

class MemberDeleteAction
{

    use AsAction;

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
        static::dispatch($member->id);

        return redirect()->back();
    }

    /**
     * @return array<int, object>
     */
    public function getJobMiddleware(int $memberId): array
    {
        $member = Member::findOrFail($memberId);

        return [
            WithJobState::make('member')
                ->before('Lösche Mitglied ' . $member->fullname)
                ->after('Mitglied ' . $member->fullname . ' gelöscht')
                ->shouldReload(),
        ];
    }
}
