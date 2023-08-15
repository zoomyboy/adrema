<?php

namespace App\Member\Actions;

use App\Lib\Events\ClientMessage;
use App\Maildispatcher\Actions\ResyncAction;
use App\Member\Member;
use Illuminate\Http\RedirectResponse;
use Lorisleiva\Actions\Concerns\AsAction;

class MemberDeleteAction
{

    use AsAction;

    public function handle(Member $member): RedirectResponse
    {
        if ($member->nami_id) {
            NamiDeleteMemberAction::dispatch($member->nami_id);
        }

        $member->delete();
        ResyncAction::dispatch();
        ClientMessage::make('Mitglied ' . $member->fullname . ' gelöscht.')->shouldReload()->dispatch();

        return redirect()->back();
    }
}
