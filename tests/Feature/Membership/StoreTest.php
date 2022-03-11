<?php

namespace Tests\Feature\Membership;

use App\Activity;
use App\Group;
use App\Member\Member;
use App\Subactivity;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Zoomyboy\LaravelNami\Fakes\MemberFake;
use Zoomyboy\LaravelNami\Fakes\MembershipFake;

class StoreTest extends TestCase
{
    use DatabaseTransactions;

    public function testItCanCreateAMembership(): void
    {
        Carbon::setTestNow(Carbon::parse('2022-02-03 03:00:00'));
        $this->withoutExceptionHandling()->login()->loginNami();
        app(MembershipFake::class)->createsSuccessfully(6, 133);
        app(MemberFake::class)->shows(1400, 6, ['version' => 1506]);
        $member = Member::factory()
            ->defaults()
            ->for(Group::factory()->inNami(1400))
            ->inNami(6)
            ->createOne();
        $activity = Activity::factory()
            ->inNami(1)
            ->hasAttached(Subactivity::factory()->inNami(2))
            ->createOne();

        $this->post("/member/{$member->id}/membership", [
            'activity_id' => $activity->id,
            'subactivity_id' => $activity->subactivities->first()->id,
        ]);

        $this->assertEquals(1506, $member->fresh()->version);
        $this->assertDatabaseHas('memberships', [
            'member_id' => $member->id,
            'activity_id' => $activity->id,
            'subactivity_id' => $activity->subactivities->first()->id,
            'nami_id' => 133,
        ]);
        app(MembershipFake::class)->assertCreated(6, [
            'untergliederungId' => 2,
            'taetigkeitId' => 1,
            'gruppierungId' => 1400,
            'aktivVon' => '2022-02-03T00:00:00',
            'aktivBis' => null,
        ]);
    }
}
