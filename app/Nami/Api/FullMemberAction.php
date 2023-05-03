<?php

namespace App\Nami\Api;

use App\Member\Data\FullMember;
use Illuminate\Support\Facades\Redis;
use Lorisleiva\Actions\Concerns\AsAction;
use Lorisleiva\Actions\Decorators\JobDecorator;
use Zoomyboy\LaravelNami\Api;

class FullMemberAction
{
    use AsAction;

    public function handle(Api $api, int $groupId, int $memberId, ?string $redisKey = null): FullMember
    {
        $fullMember = FullMember::from([
            'member' => MemberAction::run($api, $groupId, $memberId),
            'memberships' => MembershipsOfAction::run($api, $memberId),
            'courses' => CoursesOfAction::run($api, $memberId),
        ]);

        if ($redisKey) {
            Redis::rpush($redisKey, $fullMember->toJson());
        }

        return $fullMember;
    }

    public function configureJob(JobDecorator $job): void
    {
        $job->setTries(8);
    }
}
