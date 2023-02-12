<?php

namespace App\Initialize;

use App\Actions\InsertMemberAction;
use App\Actions\PullCoursesAction;
use App\Actions\PullMembershipsAction;
use App\Nami\Api\MemberAction;
use App\Setting\NamiSettings;
use DB;
use Illuminate\Console\Command;
use Lorisleiva\Actions\Concerns\AsAction;
use Zoomyboy\LaravelNami\Api;
use Zoomyboy\LaravelNami\Data\MemberEntry as NamiMember;
use Zoomyboy\LaravelNami\Exceptions\Skippable;

class InitializeMember
{
    use AsAction;

    public function handle(Api $api): void
    {
        $allMembers = collect([]);

        $jobs = $api->search([])->map(function (NamiMember $member) use ($api) {
            return MemberAction::makeJob($api, $member->groupId, $member->id);
        })->toArray();

        $batch = Bus::batch($jobs)
            // ->then(function (Batch $batch) {
            //     $localMember = InsertMemberAction::run();
            // })->catch(function (Batch $batch, Throwable $e) {
            //     // First batch job failure detected...
            // })->finally(function (Batch $batch) {
            //     // The batch has finished executing...
            // })
            ->dispatch();


            try {
            } catch (Skippable $e) {
                return;
            }

            // app(PullMembershipsAction::class)->handle($localMember);
            // app(PullCoursesAction::class)->handle($localMember);
        });

        $batch = Bus::batch([
    new ImportCsv(1, 100),
    new ImportCsv(101, 200),
    new ImportCsv(201, 300),
    new ImportCsv(301, 400),
    new ImportCsv(401, 500),

    }
}
