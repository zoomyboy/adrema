<?php

namespace App\Member\Controllers;

use App\Actions\MemberPullAction;
use App\Http\Controllers\Controller;
use App\Member\Member;
use App\Setting\NamiSettings;
use Illuminate\Http\RedirectResponse;

class MemberResyncController extends Controller
{
    public function __invoke(Member $member, NamiSettings $settings): RedirectResponse
    {
        if ($member->hasNami) {
            app(MemberPullAction::class)->api($settings->login())->member($member->group->nami_id, $member->nami_id)->execute();
        }

        return redirect()->route('member.edit', ['member' => $member])->success('Mitglied aktualisiert');
    }
}
