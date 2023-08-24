<?php

namespace Tests\EndToEnd;

use App\Activity;
use App\Group;
use App\Maildispatcher\Actions\ResyncAction;
use App\Member\Member;
use App\Member\Membership;
use App\Membership\Actions\MembershipDestroyAction;
use App\Membership\Actions\MembershipStoreAction;
use App\Membership\Actions\SyncAction;
use App\Subactivity;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;
use Throwable;
use Zoomyboy\LaravelNami\Fakes\MembershipFake;

class SyncActionTest extends TestCase
{

    use DatabaseMigrations;

    public function testItFiresActionJobWhenUsingController(): void
    {
        Queue::fake();
        $this->login()->loginNami()->withoutExceptionHandling();
        $member = Member::factory()->defaults()->create();
        $activity = Activity::factory()->create();
        $subactivity = Subactivity::factory()->create();
        $group = Group::factory()->create();

        $this->postJson('/api/membership/sync', [
            'members' => [$member->id],
            'activity_id' => $activity->id,
            'subactivity_id' => $subactivity->id,
            'group_id' => $group->id,
        ]);
        SyncAction::assertPushed(fn ($action, $params) => $params[0]->is($group) && $params[1]->is($activity) && $params[2]->is($subactivity) && $params[3][0] === $member->id);
    }

    public function testItCreatesAMembership(): void
    {
        MembershipDestroyAction::partialMock()->shouldReceive('handle')->never();
        MembershipStoreAction::partialMock()->shouldReceive('handle')->once();
        $member = Member::factory()->defaults()->create();
        $activity = Activity::factory()->create();
        $subactivity = Subactivity::factory()->create();
        $group = Group::factory()->create();

        SyncAction::run($group, $activity, $subactivity, [$member->id]);
    }

    public function testItDeletesAMembership(): void
    {
        MembershipDestroyAction::partialMock()->shouldReceive('handle')->once();
        MembershipStoreAction::partialMock()->shouldReceive('handle')->never();
        ResyncAction::partialMock()->shouldReceive('handle')->once();

        $member = Member::factory()->defaults()->has(Membership::factory()->inLocal('Leiter*in', 'Rover'))->create();

        SyncAction::run($member->memberships->first()->group, $member->memberships->first()->activity, $member->memberships->first()->subactivity, []);
    }

    public function testItRollsbackWhenDeletionFails(): void
    {
        app(MembershipFake::class)
            ->shows(3, ['id' => 55])
            ->shows(3, ['id' => 56])
            ->destroysSuccessfully(3, 55)
            ->failsDeleting(3, 56);
        $this->login()->loginNami();

        $member = Member::factory()->defaults()->inNami(3)
            ->has(Membership::factory()->in('Leiter*in', 10, 'Rover', 11)->inNami(55))
            ->has(Membership::factory()->in('Leiter*in', 10, 'Jungpfadfinder', 12)->inNami(56))
            ->create();

        try {
            SyncAction::run($member->memberships->first()->group, $member->memberships->first()->activity, $member->memberships->first()->subactivity, []);
        } catch (Throwable $e) {
        }
        $this->assertDatabaseCount('memberships', 2);
    }
}
