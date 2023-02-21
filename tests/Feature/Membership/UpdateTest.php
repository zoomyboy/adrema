<?php

namespace Tests\Feature\Membership;

use App\Activity;
use App\Group;
use App\Member\Member;
use App\Member\Membership;
use App\Subactivity;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\RequestFactories\MembershipRequestFactory;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(Carbon::parse('2022-02-03 03:00:00'));
        $this->login()->loginNami();
    }

    public function testItUpdatesAMembership(): void
    {
        $this->withoutExceptionHandling();
        $activity = Activity::factory()->hasAttached(Subactivity::factory())->create();
        $member = Member::factory()
            ->defaults()
            ->has(Membership::factory()->for($activity)->for($activity->subactivities->first()))
            ->for(Group::factory()->inNami(1400))
            ->inNami(6)
            ->create();
        $membership = $member->memberships->first();

        $response = $this->from('/member')->patch(
            "/member/{$member->id}/membership/{$membership->id}",
            MembershipRequestFactory::new()->promise(now())->in($membership->activity, $membership->subactivity)->create()
        );

        $response->assertRedirect('/member');
        $this->assertDatabaseHas('memberships', [
            'member_id' => $member->id,
            'activity_id' => $activity->id,
            'subactivity_id' => $activity->subactivities->first()->id,
            'promised_at' => now()->format('Y-m-d'),
        ]);
    }
}
