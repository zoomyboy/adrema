<?php

namespace App\Initialize;

use App\Actions\InsertMemberAction;
use App\Actions\PullCoursesAction;
use App\Actions\PullMembershipsAction;
use App\Nami\Api\CompleteMemberToRedisJob;
use App\Setting\NamiSettings;
use DB;
use Illuminate\Bus\Batch;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Redis;
use Lorisleiva\Actions\Concerns\AsAction;
use Zoomyboy\LaravelNami\Api;
use Zoomyboy\LaravelNami\Data\MemberEntry as NamiMember;

class InitializeMembers
{
    use AsAction;

    public string $commandSignature = 'member:pull';

    public function handle(Api $api): void
    {
        $allMembers = collect([]);
        Redis::delete('members');

        $jobs = $api->search([])->map(function (NamiMember $member) use ($api) {
            return new CompleteMemberToRedisJob($api, $member->groupId, $member->id);
        })->toArray();

        $batch = Bus::batch($jobs)
            ->finally(function (Batch $batch) {
                dd(Redis::get('members'));
            })
            ->dispatch();
        //     $localMember = InsertMemberAction::run();
            // })->catch(function (Batch $batch, Throwable $e) {
            //     // First batch job failure detected...
            // })->finally(function (Batch $batch) {
            //     // The batch has finished executing...
            // })

            // app(PullMembershipsAction::class)->handle($localMember);
            // app(PullCoursesAction::class)->handle($localMember);
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
