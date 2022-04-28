<?php

namespace App\Initialize;

use App\Actions\MemberPullAction;
use Zoomyboy\LaravelNami\Api;
use Zoomyboy\LaravelNami\Member as NamiMember;

class InitializeMembers
{
    private Api $api;

    public function __construct(Api $api)
    {
        $this->api = $api;
    }

    public function handle(): void
    {
        $allMembers = collect([]);

        $this->api->search([])->each(
            fn (NamiMember $member) => app(MemberPullAction::class)->api($this->api)->member($member->group_id, $member->id)->execute()
        );
    }
}
