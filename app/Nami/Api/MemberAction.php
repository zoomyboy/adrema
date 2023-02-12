<?php

namespace App\Nami\Api;

use Lorisleiva\Actions\Concerns\AsAction;
use Zoomyboy\LaravelNami\Api;
use Zoomyboy\LaravelNami\Data\Member;

class MemberAction
{
    use AsAction;

    public function handle(Api $api, int $groupId, int $memberId): Member
    {
        return $api->member($groupId, $memberId);
    }
}
