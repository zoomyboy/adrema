<?php

namespace Tests\Feature\Membership;

use App\Activity;
use App\Group;
use App\Lib\Events\JobFinished;
use App\Lib\Events\JobStarted;
use App\Lib\Events\ReloadTriggered;
use App\Member\Member;
use App\Subactivity;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Event;
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
            MembershipRequestFactory::new()->promise(now())->in($activity, $activity->subactivities->first())->group($member->group)->create()
        );

        $response->assertOk();
        $this->assertEquals(1506, $member->fresh()->version);
        $this->assertDatabaseHas('memberships', [
            'member_id' => $member->id,
            'activity_id' => $activity->id,
            'subactivity_id' => $activity->subactivities->first()->id,
            'nami_id' => 133,
            'promised_at' => now()->format('Y-m-d'),
            'group_id' => $member->group->id,
        ]);
        app(MembershipFake::class)->assertCreated(6, [
            'untergliederungId' => 4,
            'taetigkeitId' => 6,
            'gruppierungId' => 1400,
            'aktivVon' => '2022-02-03T00:00:00',
            'aktivBis' => null,
        ]);
    }

    public function testItFiresJobEvents(): void
    {
        Event::fake([JobStarted::class, JobFinished::class, ReloadTriggered::class]);
        $this->withoutExceptionHandling();
        $member = Member::factory()->defaults()->for(Group::factory())->createOne();
        $activity = Activity::factory()->hasAttached(Subactivity::factory())->createOne();

        $this->from('/member')->post(
            "/member/{$member->id}/membership",
            MembershipRequestFactory::new()->in($activity, $activity->subactivities->first())->group($member->group)->create()
        );

        Event::assertDispatched(JobStarted::class, fn ($event) => $event->broadcastOn()[0]->name === 'jobs' && $event->message !== null);
        Event::assertDispatched(JobFinished::class, fn ($event) => $event->broadcastOn()[0]->name === 'jobs' && $event->message !== null);
        Event::assertDispatched(ReloadTriggered::class, fn ($event) => ['member', 'membership'] === $event->channels->toArray());
    }

    public function testItDoesntFireNamiWhenMembershipIsLocal(): void
    {
        $this->withoutExceptionHandling();
        $member = Member::factory()
            ->defaults()
            ->for(Group::factory()->inNami(1400))
            ->inNami(6)
            ->create();
        $activity = Activity::factory()->hasAttached(Subactivity::factory())->create();

        $this->from('/member')->post(
            "/member/{$member->id}/membership",
            MembershipRequestFactory::new()->in($activity, $activity->subactivities->first())->group($member->group)->create()
        );

        $this->assertDatabaseHas('memberships', [
            'member_id' => $member->id,
            'activity_id' => $activity->id,
            'subactivity_id' => $activity->subactivities->first()->id,
            'nami_id' => null,
        ]);
    }

    public function testItDoesntFireNamiWhenSubactivityIsLocal(): void
    {
        $this->withoutExceptionHandling();
        $member = Member::factory()
            ->defaults()
            ->for(Group::factory()->inNami(1400))
            ->inNami(6)
            ->create();
        $activity = Activity::factory()->inNami(666)->hasAttached(Subactivity::factory())->create();

        $this->from('/member')->post(
            "/member/{$member->id}/membership",
            MembershipRequestFactory::new()->in($activity, $activity->subactivities->first())->group($member->group)->create()
        );

        $this->assertDatabaseHas('memberships', [
            'member_id' => $member->id,
            'activity_id' => $activity->id,
            'subactivity_id' => $activity->subactivities->first()->id,
            'nami_id' => null,
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

    public function testItCanAddAnotherGroup(): void
    {
        app(MembershipFake::class)->createsSuccessfully(6, 133);
        app(MemberFake::class)->shows(1400, 6, ['version' => 1506]);
        $member = Member::factory()->defaults()->for(Group::factory()->inNami(1400))->inNami(6)->create();
        $group = Group::factory()->inNami(1401)->create();
        $activity = Activity::factory()->inNami(7)->hasAttached(Subactivity::factory()->inNami(8))->create();

        $this->post(
            "/member/{$member->id}/membership",
            MembershipRequestFactory::new()->in($activity, $activity->subactivities->first())->group($group)->create()
        );

        $this->assertDatabaseHas('memberships', [
            'group_id' => $group->id,
        ]);
        app(MembershipFake::class)->assertCreated(6, [
            'gruppierungId' => 1401,
        ]);
    }

    public function testGroupIsRequired(): void
    {
        $member = Member::factory()->defaults()->for(Group::factory()->inNami(1400))->inNami(6)->create();

        $response = $this->post(
            "/member/{$member->id}/membership",
            [],
        );

        $response->assertSessionHasErrors(['group_id' => 'Gruppierung ist erforderlich.']);
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
            MembershipRequestFactory::new()->in($activity, null)->group($member->group)->create()
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

    public function testItStoresNamiActivityAndSubactivityForNonNamiMember(): void
    {
        $this->withoutExceptionHandling();
        $member = Member::factory()->defaults()->create();
        $activity = Activity::factory()->hasAttached(Subactivity::factory()->inNami(7))->inNami(6)->create();

        $this->post(
            "/member/{$member->id}/membership",
            MembershipRequestFactory::new()->in($activity, $activity->subactivities->first())->group($member->group)->create()
        )->assertOk();
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
            MembershipRequestFactory::new()->in($activity, $activity->subactivities->first())->group($member->group)->create()
        );

        $this->assertErrors(['nami' => $validationError], $response);

        $this->assertDatabaseMissing('memberships', [
            'member_id' => $member->id,
        ]);
    }
}
