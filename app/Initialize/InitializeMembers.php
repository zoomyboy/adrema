<?php

namespace App\Initialize;

use App\Actions\PullCoursesAction;
use App\Actions\PullMemberAction;
use App\Actions\PullMembershipsAction;
use App\Setting\NamiSettings;
use DB;
use Illuminate\Console\Command;
use Lorisleiva\Actions\Concerns\AsAction;
use Zoomyboy\LaravelNami\Api;
use Zoomyboy\LaravelNami\Data\MemberEntry as NamiMember;
use Zoomyboy\LaravelNami\Exceptions\Skippable;

class InitializeMembers
{
    use AsAction;

    public string $commandSignature = 'member:pull';

    public function handle(Api $api): void
    {
        $allMembers = collect([]);

        $api->search([])->each(function (NamiMember $member) {
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

    public function asCommand(Command $command): int
    {
        $this->handle(app(NamiSettings::class)->login());

        return 0;
    }
}
