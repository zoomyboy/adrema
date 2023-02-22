<?php

namespace App\Member\Actions;

use App\Actions\InsertCoursesAction;
use App\Actions\InsertMemberAction;
use App\Actions\InsertMembershipsAction;
use App\Member\Data\FullMember;
use Lorisleiva\Actions\Concerns\AsAction;

class InsertFullMemberAction
{
    use AsAction;

    public string $jobQueue = 'single';

    public function handle(FullMember $member): void
    {
        $localMember = InsertMemberAction::run($member->member);
        InsertMembershipsAction::run($localMember, $member->memberships->toCollection());
        InsertCoursesAction::run($localMember, $member->courses->toCollection());
    }
}
