<?php

namespace App\Initialize;

use App\Initialize\Actions\ProcessRedisAction;
use App\Nami\Api\CompleteMemberToRedisJob;
use App\Setting\NamiSettings;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Redis;
use Lorisleiva\Actions\Concerns\AsAction;
use Zoomyboy\LaravelNami\Api;
use Zoomyboy\LaravelNami\Data\MemberEntry as NamiMemberEntry;

class InitializeMembers
{
    use AsAction;

    public string $commandSignature = 'member:pull';
    public string $jobQueue = 'long';

    public function handle(Api $api): void
    {
        Redis::delete('members');

        $jobs = $api->search([])->map(function (NamiMemberEntry $member) use ($api) {
            return new CompleteMemberToRedisJob($api, $member->groupId, $member->id);
        })->toArray();

        Bus::batch($jobs)
            ->finally(function () {
                foreach (Redis::lrange('members', 0, -1) as $data) {
                    ProcessRedisAction::dispatch(json_decode($data, true));
                }
            })
            ->onQueue('long')
            ->allowFailures()
            ->dispatch();
    }

    public function asCommand(): int
    {
        $this->handle(app(NamiSettings::class)->login());

        return 0;
    }
}
