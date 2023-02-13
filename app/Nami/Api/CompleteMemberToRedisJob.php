<?php

namespace App\Nami\Api;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Redis;
use Log;
use Zoomyboy\LaravelNami\Api;

class CompleteMemberToRedisJob implements ShouldQueue
{
    use Batchable;
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        private Api $api,
        private int $groupId,
        private int $memberId,
    ) {
    }

    public function handle(): void
    {
        if ($this->batch()->cancelled()) {
            Log::debug('Cancelling batch');

            return;
        }

        Redis::rpush('members', collect([
            'member' => MemberAction::run($this->api, $this->groupId, $this->memberId),
            'memberships' => MembershipsOfAction::run($this->api, $this->memberId),
            'courses' => CoursesOfAction::run($this->api, $this->memberId),
        ])->toJson());
    }
}
