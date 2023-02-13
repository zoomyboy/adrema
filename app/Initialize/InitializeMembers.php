<?php

namespace App\Initialize;

use App\Actions\InsertCoursesAction;
use App\Actions\InsertMemberAction;
use App\Actions\InsertMembershipsAction;
use App\Nami\Api\CompleteMemberToRedisJob;
use App\Setting\NamiSettings;
use DB;
use Illuminate\Bus\Batch;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Redis;
use Lorisleiva\Actions\Concerns\AsAction;
use Zoomyboy\LaravelNami\Api;
use Zoomyboy\LaravelNami\Data\Course as NamiCourse;
use Zoomyboy\LaravelNami\Data\Member as NamiMember;
use Zoomyboy\LaravelNami\Data\MemberEntry as NamiMemberEntry;
use Zoomyboy\LaravelNami\Data\MembershipEntry as NamiMembershipEntry;

class InitializeMembers
{
    use AsAction;

    public string $commandSignature = 'member:pull';
    public string $jobQueue = 'long';

    public function handle(Api $api): void
    {
        $allMembers = collect([]);
        Redis::delete('members');

        $jobs = $api->search([])->map(function (NamiMemberEntry $member) use ($api) {
            return new CompleteMemberToRedisJob($api, $member->groupId, $member->id);
        })->toArray();

        $batch = Bus::batch($jobs)
            ->finally(function (Batch $batch) {
                foreach (Redis::lrange('members', 0, -1) as $data) {
                    try {
                        $data = json_decode($data, true);
                        $localMember = InsertMemberAction::run(NamiMember::from($data['member']));
                        InsertMembershipsAction::run(
                            $localMember,
                            collect($data['memberships'])->map(fn ($membership) => NamiMembershipEntry::from($membership)),
                        );
                        InsertCoursesAction::run(
                            $localMember,
                            collect($data['courses'])->map(fn ($course) => NamiCourse::from($course)),
                        );
                    } catch (Skippable $e) {
                        continue;
                    }
                }
            })
            ->onQueue('long')
            ->dispatch();
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
