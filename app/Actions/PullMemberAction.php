<?php

namespace App\Actions;

use App\Member\Member;
use App\Nami\Api\MemberAction;
use App\Setting\NamiSettings;
use Lorisleiva\Actions\Concerns\AsAction;
use Zoomyboy\LaravelNami\Api;

class PullMemberAction
{
    use AsAction;

    public function handle(int $groupId, int $memberId): Member
    {
        return InsertMemberAction::run(MemberAction::run($this->api(), $groupId, $memberId));
    }

    private function api(): Api
    {
        return app(NamiSettings::class)->login();
    }
}
