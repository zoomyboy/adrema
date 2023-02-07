<?php

namespace Tests\Feature\Member;

use App\Actions\PullCoursesAction;
use App\Actions\PullMemberAction;
use App\Actions\PullMembershipsAction;
use App\Group;
use App\Member\Member;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ResyncTest extends TestCase
{
    use DatabaseTransactions;

    public function testItCanResyncAMember(): void
    {
        $this->login()->loginNami();
        $member = Member::factory()->defaults()->for(Group::factory()->inNami(32))->inNami(33)->create();
        PullMemberAction::shouldRun()->once()->with(32, 33)->andReturn($member);
        PullMembershipsAction::shouldRun()->once()->with($member);
        PullCoursesAction::shouldRun()->never();

        $response = $this->from('/member')->get(route('member.resync', ['member' => $member]));

        $response->assertRedirect('/member');
    }

    public function testItReturnsErrorWhenMemberIsNotInNami(): void
    {
        $this->login()->loginNami();
        $member = Member::factory()->defaults()->create();

        PullMemberAction::shouldRun()->never();
        PullMembershipsAction::shouldRun()->never();
        PullCoursesAction::shouldRun()->never();

        $response = $this->from('/member')->get(route('member.resync', ['member' => $member]));

        $response->assertRedirect('/member');
    }
}
