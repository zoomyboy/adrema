<?php

namespace Tests\Feature\Membership;

use App\Activity;
use App\Group;
use App\Member\Member;
use App\Subactivity;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\RequestFactories\MembershipRequestFactory;
use Tests\TestCase;
use Zoomyboy\LaravelNami\Fakes\MemberFake;
use Zoomyboy\LaravelNami\Fakes\MembershipFake;

class StoreTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(Carbon::parse('2022-02-03 03:00:00'));
        $this->login()->loginNami();
    }

    public function testItCreatesAMembership(): void
    {
        $this->withoutExceptionHandling();
        app(MembershipFake::class)->createsSuccessfully(6, 133);
        app(MemberFake::class)->shows(1400, 6, ['version' => 1506]);
        $member = Member::factory()
            ->defaults()
            ->for(Group::factory()->inNami(1400))
            ->inNami(6)
            ->create();
        $activity = Activity::factory()
            ->inNami(6)
            ->hasAttached(Subactivity::factory()->inNami(4))
            ->createOne();

        $response = $this->from('/member')->post(
            "/member/{$member->id}/membership",
            MembershipRequestFactory::new()->in($activity, $activity->subactivities->first())->create()
        );

        $response->assertRedirect('/member');
        $this->assertEquals(1506, $member->fresh()->version);
        $this->assertDatabaseHas('memberships', [
            'member_id' => $member->id,
            'activity_id' => $activity->id,
            'subactivity_id' => $activity->subactivities->first()->id,
            'nami_id' => 133,
        ]);
        app(MembershipFake::class)->assertCreated(6, [
            'untergliederungId' => 4,
            'taetigkeitId' => 6,
            'gruppierungId' => 1400,
            'aktivVon' => '2022-02-03T00:00:00',
            'aktivBis' => null,
        ]);
    }

    public function testActivityIsRequired(): void
    {
        $member = Member::factory()
            ->defaults()
            ->for(Group::factory()->inNami(1400))
            ->inNami(6)
            ->create();

        $response = $this->post(
            "/member/{$member->id}/membership",
            MembershipRequestFactory::new()->missingAll()->create(),
        );

        $this->assertErrors(['activity_id' => 'Tätigkeit ist erforderlich.'], $response);
    }

    public function testActivityShouldBeValid(): void
    {
        $member = Member::factory()
            ->defaults()
            ->for(Group::factory()->inNami(1400))
            ->inNami(6)
            ->create();

        $response = $this->post(
            "/member/{$member->id}/membership",
            MembershipRequestFactory::new()->invalidActivity()->create(),
        );

        $this->assertErrors(['activity_id' => 'Tätigkeit ist nicht vorhanden.'], $response);
    }

    public function testSubactivityShouldBeFromActivity(): void
    {
        $member = Member::factory()
            ->defaults()
            ->for(Group::factory()->inNami(1400))
            ->inNami(6)
            ->create();

        $response = $this->post(
            "/member/{$member->id}/membership",
            MembershipRequestFactory::new()->unmatchingSubactivity()->create(),
        );

        $this->assertErrors(['subactivity_id' => 'Untertätigkeit ist nicht vorhanden.'], $response);
    }

    public function testSubactivityCanBeEmpty(): void
    {
        $this->withoutExceptionHandling();
        app(MembershipFake::class)->createsSuccessfully(6, 133);
        app(MemberFake::class)->shows(1400, 6, ['version' => 1506]);
        $member = Member::factory()
            ->defaults()
            ->for(Group::factory()->inNami(1400))
            ->inNami(6)
            ->create();
        $activity = Activity::factory()
            ->inNami(6)
            ->createOne();

        $this->post(
            "/member/{$member->id}/membership",
            MembershipRequestFactory::new()->in($activity, null)->create()
        );

        $this->assertEquals(1506, $member->fresh()->version);
        $this->assertDatabaseHas('memberships', [
            'member_id' => $member->id,
            'activity_id' => $activity->id,
            'subactivity_id' => null,
            'nami_id' => 133,
        ]);
        app(MembershipFake::class)->assertCreated(6, [
            'untergliederungId' => null,
            'taetigkeitId' => 6,
            'gruppierungId' => 1400,
            'aktivVon' => '2022-02-03T00:00:00',
            'aktivBis' => null,
        ]);
    }

    /**
     * @testWith ["namierror<br>", "namierror&lt;br&gt;"]
     *           ["", "Erstellen der Mitgliedschaft fehlgeschlagen"]
     */
    public function testItReturnsNamiError(string $namiError, string $validationError): void
    {
        app(MembershipFake::class)->failsCreating(6, $namiError);
        $member = Member::factory()
            ->defaults()
            ->for(Group::factory()->inNami(1400))
            ->inNami(6)
            ->create();
        $activity = Activity::factory()
            ->inNami(6)
            ->hasAttached(Subactivity::factory()->inNami(4))
            ->createOne();

        $response = $this->post(
            "/member/{$member->id}/membership",
            MembershipRequestFactory::new()->in($activity, $activity->subactivities->first())->create()
        );

        $this->assertErrors(['nami' => $validationError], $response);

        $this->assertDatabaseMissing('memberships', [
            'member_id' => $member->id,
        ]);
    }
}
