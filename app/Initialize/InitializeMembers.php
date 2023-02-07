<?php

namespace App\Initialize;

use App\Actions\PullCoursesAction;
use App\Actions\PullMemberAction;
use App\Actions\PullMembershipsAction;
use DB;
use Zoomyboy\LaravelNami\Api;
use Zoomyboy\LaravelNami\Exceptions\Skippable;

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

        $this->api->search([])->each(function ($member) {
            try {
                $localMember = app(PullMemberAction::class)->handle($member->groupId, $member->id);
            } catch (Skippable $e) {
                return;
            }

            app(PullMembershipsAction::class)->handle($localMember);
            app(PullCoursesAction::class)->handle($localMember);
        });
    }

    public function restore(): void
    {
        DB::table('payments')->delete();
        DB::table('course_members')->delete();
        DB::table('memberships')->delete();
        DB::table('members')->delete();
    }
}
