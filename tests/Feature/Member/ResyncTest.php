<?php

namespace Tests\Feature\Member;

use App\Group;
use App\Member\Actions\InsertFullMemberAction;
use App\Member\Data\FullMember;
use App\Member\Member;
use App\Nami\Api\FullMemberAction;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Zoomyboy\LaravelNami\Api;
use Zoomyboy\LaravelNami\Fakes\MemberFake;

class ResyncTest extends TestCase
{
    use DatabaseTransactions;

    private Api $api;

    public function testItCanResyncAMember(): void
    {
        $this->api = $this->createStub(Api::class);
        $this->login()->loginNami();
        app(MemberFake::class)->shows(32, 33);
        $fullMember = FullMember::from(['courses' => [], 'memberships' => [], 'member' => $this->api->member(32, 33)]);
        FullMemberAction::shouldRun()->once()->andReturn($fullMember);
        InsertFullMemberAction::shouldRun()->once();
        $member = Member::factory()->defaults()->for(Group::factory()->inNami(32))->inNami(33)->create();

        $response = $this->from('/member')->get(route('member.resync', ['member' => $member]));

        $response->assertRedirect('/member');
    }

}
