<?php

namespace App\Initialize;

use App\Member\Actions\InsertFullMemberAction;
use App\Member\Data\FullMember;
use App\Member\Member;
use App\Nami\Api\FullMemberAction;
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
        $settings = app(NamiSettings::class);
        Redis::delete('members');

        $memberIds = $api->search($settings->search_params)->map(fn ($member) => $member->id)->toArray();
        Member::remote()->whereNotIn('nami_id', $memberIds)->get()->each->delete();

        $jobs = $api->search($settings->search_params)->map(function (NamiMemberEntry $member) use ($api) {
            return FullMemberAction::makeJob($api, $member->groupId, $member->id, 'members');
        })->toArray();

        Bus::batch($jobs)
            ->finally(function () {
                /** @var array<int, FullMember> */
                $members = array_map(fn ($member) => FullMember::from(json_decode($member, true)), Redis::lrange('members', 0, -1));

                foreach ($members as $data) {
                    InsertFullMemberAction::dispatch($data);
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
