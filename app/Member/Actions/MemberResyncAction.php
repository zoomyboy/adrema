<?php

namespace App\Member\Actions;

use App\Member\Member;
use App\Nami\Api\FullMemberAction;
use App\Setting\NamiSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class MemberResyncAction
{
    use AsAction;

    public function handle(Member $member, NamiSettings $settings): void
    {
        $api = $settings->login();

        if (!$member->hasNami || !$member->group->hasNami) {
            return;
        }

        $fullMember = FullMemberAction::run($api, $member->group->nami_id, $member->nami_id);

        InsertFullMemberAction::dispatch($fullMember);
    }

    public function asController(ActionRequest $request, Member $member): RedirectResponse|Response
    {
        $this->handle(
            $member,
            app(NamiSettings::class),
        );

        return redirect()->back();
    }
}
