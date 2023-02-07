<?php

namespace App\Member\Actions;

use App\Actions\PullMemberAction;
use App\Actions\PullMembershipsAction;
use App\Member\Member;
use App\Setting\NamiSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Zoomyboy\LaravelNami\Exceptions\Skippable;

class MemberResyncAction
{
    use AsAction;

    public function handle(Member $member, NamiSettings $settings): void
    {
        $api = $settings->login();

        if (!$member->hasNami || !$member->group->hasNami) {
            return;
        }

        try {
            $localMember = app(PullMemberAction::class)->handle($member->group->nami_id, $member->nami_id);
        } catch (Skippable $e) {
            return;
        }

        app(PullMembershipsAction::class)->handle($localMember);
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
