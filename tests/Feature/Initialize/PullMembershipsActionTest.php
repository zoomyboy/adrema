<?php

namespace Tests\Feature\Initialize;

use App\Actions\PullMembershipsAction;
use App\Activity;
use App\Country;
use App\Fee;
use App\Gender;
use App\Group;
use App\Member\Member;
use App\Member\Membership;
use App\Nationality;
use App\Payment\Subscription;
use App\Subactivity;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use Zoomyboy\LaravelNami\Fakes\ActivityFake;
use Zoomyboy\LaravelNami\Fakes\MembershipFake;
use Zoomyboy\LaravelNami\Fakes\SubactivityFake;

class PullMembershipsActionTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp(): void
    {
        parent::setUp();

        Subscription::factory()->name('test')->for(Fee::factory()->inNami(300))->create();
        Gender::factory()->inNami(303)->create();
        Country::factory()->inNami(302)->create();
        Nationality::factory()->inNami(1054)->create();
        $this->loginNami();
    }

    public function testItDoesntSyncMembershipisWhenMemberHasNoNami(): void
    {
        $member = Member::factory()->defaults()->for(Group::factory()->inNami(1000)->name('SG Wald'))->create();

        app(PullMembershipsAction::class)->handle($member);

        Http::assertSentCount(0);
    }

    public function testFetchMembershipViaOverviewIfMemberHasSameGroup(): void
    {
        $activity = Activity::factory()->inNami(1003)->name('Tätigkeit')->create();
        $member = Member::factory()->defaults()->for(Group::factory()->inNami(1000)->name('SG Wald'))->inNami(1001)->create();
        app(MembershipFake::class)->fetches(1001, [
            [
                'id' => 1077,
                'entries_aktivBis' => '',
                'entries_aktivVon' => '2021-08-22 00:00:00',
                'entries_taetigkeit' => 'Tätigkeit (1003)',
                'entries_gruppierung' => 'SG Wald',
                'entries_untergliederung' => '',
            ],
        ]);

        app(PullMembershipsAction::class)->handle($member);

        $group = Group::firstWhere('nami_id', 1000);
        $member = Member::nami(1001);
        $this->assertDatabaseHas('memberships', [
            'activity_id' => $activity->id,
            'subactivity_id' => null,
            'group_id' => $group->id,
            'from' => '2021-08-22',
            'nami_id' => 1077,
        ]);
    }

    public function testFetchMembershipsFromForeignGroup(): void
    {
        $activity = Activity::factory()->inNami(1003)->name('Tätigkeit')->create();
        $group = Group::factory()->inNami(1099)->name('Gruppe')->create();
        $member = Member::factory()->defaults()->for($group)->inNami(1001)->create();
        app(MembershipFake::class)->fetches(1001, [
            [
                'id' => 1077,
                'entries_aktivBis' => '',
                'entries_aktivVon' => '2021-08-22 00:00:00',
                'entries_taetigkeit' => 'Tätigkeit (1003)',
                'entries_gruppierung' => 'Gruppe',
                'entries_untergliederung' => '',
            ],
        ]);

        app(PullMembershipsAction::class)->handle($member);

        $this->assertDatabaseHas('memberships', [
            'group_id' => $group->id,
            'nami_id' => 1077,
        ]);
    }

    public function testFetchSingleMembership(): void
    {
        $member = Member::factory()->defaults()->for(Group::factory()->inNami(90))->inNami(1001)->create();
        $group = Group::factory()->inNami(1005)->name('Gruppe')->create();
        app(MembershipFake::class)->fetches(1001, [
            [
                'id' => 1077,
                'entries_aktivBis' => '',
                'entries_aktivVon' => '2021-08-22 00:00:00',
                'entries_taetigkeit' => 'ReferentIn (33)',
                'entries_gruppierung' => 'Gruppe',
                'entries_untergliederung' => '',
            ],
        ]);
        app(MembershipFake::class)->shows(1001, [
            'id' => 1077,
            'gruppierung' => 'Gruppe',
            'gruppierungId' => 1005,
            'taetigkeit' => 'ReferentIn',
            'taetigkeitId' => 33,
            'untergliederung' => 'Pfadfinder',
            'untergliederungId' => 55,
            'aktivVon' => '2021-08-22 00:00:00',
            'aktivBis' => '',
        ]);
        app(ActivityFake::class)->fetches(1005, [['descriptor' => 'ReferentIn2', 'id' => 33]]);
        app(SubactivityFake::class)->fetches(33, [['descriptor' => 'Pfadfinder2', 'id' => 55]]);

        app(PullMembershipsAction::class)->handle($member);

        $this->assertDatabaseHas('activities', [
            'name' => 'ReferentIn2',
            'nami_id' => 33,
        ]);
        $this->assertDatabaseHas('subactivities', [
            'name' => 'Pfadfinder2',
            'nami_id' => 55,
        ]);
        $this->assertDatabaseHas('memberships', [
            'group_id' => $group->id,
            'activity_id' => Activity::nami(33)->id,
            'subactivity_id' => Subactivity::nami(55)->id,
        ]);
    }

    public function testUpdateExistingMembership(): void
    {
        $member = Member::factory()
            ->defaults()
            ->for(Group::factory()->name('Gruppe')->inNami(90))
            ->inNami(1001)
            ->has(Membership::factory()->in('Leiter', 50, 'Rover', 60)->inNami(5060))
            ->create();
        app(MembershipFake::class)->fetches(1001, [
            [
                'id' => 5060,
                'entries_aktivBis' => '2021-08-23 00:00:00',
                'entries_aktivVon' => '2021-08-22 00:00:00',
                'entries_taetigkeit' => 'Leiter (50)',
                'entries_gruppierung' => 'Gruppe',
                'entries_untergliederung' => 'Rover',
            ],
        ]);

        app(PullMembershipsAction::class)->handle($member);

        $this->assertDatabaseCount('memberships', 1);
        $this->assertDatabaseHas('memberships', [
            'nami_id' => 5060,
            'from' => '2021-08-22 00:00:00',
            'to' => '2021-08-23 00:00:00',
        ]);
    }

    public function testDeleteExistingMembership(): void
    {
        $member = Member::factory()
            ->defaults()
            ->for(Group::factory()->name('Gruppe')->inNami(90))
            ->inNami(1001)
            ->has(Membership::factory()->in('Leiter', 50, 'Rover', 60)->inNami(5060))
            ->create();
        app(MembershipFake::class)->fetches(1001, []);

        app(PullMembershipsAction::class)->handle($member);

        $this->assertDatabaseCount('memberships', 0);
    }

    public function testDontDeleteLocalMemberships(): void
    {
        $member = Member::factory()
            ->defaults()
            ->for(Group::factory()->name('Gruppe')->inNami(90))
            ->inNami(1001)
            ->has(Membership::factory()->inLocal('Leiter', 'Rover')->local())
            ->create();
        app(MembershipFake::class)->fetches(1001, []);

        app(PullMembershipsAction::class)->handle($member);

        $this->assertDatabaseCount('memberships', 1);
    }
}
