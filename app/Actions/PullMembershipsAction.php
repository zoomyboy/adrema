<?php

namespace App\Actions;

use App\Member\Member;
use App\Nami\Api\MembershipsOfAction;
use App\Setting\NamiSettings;
use Lorisleiva\Actions\Concerns\AsAction;
use Zoomyboy\LaravelNami\Api;

class PullMembershipsAction
{
    use AsAction;

    public function handle(Member $member): void
    {
        if (!$member->hasNami) {
            return;
        }

        InsertMembershipsAction::run($member, MembershipsOfAction::run($this->api(), $member->nami_id));
    }

    private function api(): Api
    {
        return app(NamiSettings::class)->login();
    }
}
