<?php

namespace Tests\Feature\Member;

use App\Actions\PullMemberAction;
use App\Actions\PullMembershipsAction;
use App\Activity;
use App\Confession;
use App\Country;
use App\Fee;
use App\Group;
use App\Member\Actions\NamiPutMemberAction;
use App\Member\Member;
use App\Nationality;
use App\Payment\Subscription;
use App\Subactivity;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use Zoomyboy\LaravelNami\Fakes\MemberFake;

class UpdateTest extends TestCase
{
    use DatabaseTransactions;

    public function testItRedirectsToMemberOverview(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami();
        $member = $this->member();
        $this->fakeRequest();
        NamiPutMemberAction::allowToRun();

        $response = $this
            ->from("/member/{$member->id}")
            ->patch("/member/{$member->id}", array_merge($member->getAttributes(), ['has_nami' => true]));

        $response->assertRedirect('/member');
        NamiPutMemberAction::spy()->shouldHaveReceived('handle')->withArgs(
            fn (Member $memberParam, ?Activity $activityParam, ?Subactivity $subactivityParam) => $memberParam->is($member)
                && null === $activityParam
                && null === $subactivityParam
        )->once();
    }

    public function testItChecksVersion(): void
    {
        $this->login()->loginNami()->withoutExceptionHandling();
        $member = $this->member();
        $member->update(['version' => 43]);
        $this->fakeRequest();

        $response = $this
            ->from("/member/{$member->id}")
            ->patch("/member/{$member->id}", array_merge($member->getAttributes(), ['has_nami' => true, 'firstname' => '::firstname::']));

        $response->assertRedirect("/member/{$member->id}/edit?conflict=1");
    }

    public function testItUpdatesPhoneNumber(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami();
        $member = $this->member();
        $this->fakeRequest();
        NamiPutMemberAction::allowToRun();

        $this->patch("/member/{$member->id}", array_merge($member->getAttributes(), [
            'main_phone' => '02103 4455129',
            'fax' => '02103 4455130',
            'children_phone' => '02103 4455130',
            'has_nami' => true,
        ]));

        $this->assertDatabaseHas('members', [
            'main_phone' => '+49 2103 4455129',
            'fax' => '+49 2103 4455130',
            'children_phone' => '+49 2103 4455130',
        ]);
    }

    public function testItSetsLocationToNull(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami();
        $member = $this->member(['location' => 'Hilden', 'nami_id' => null]);
        $this->fakeRequest();
        NamiPutMemberAction::allowToRun();

        $this->patch("/member/{$member->id}", array_merge($member->getAttributes(), [
            'location' => null,
        ]));

        $this->assertDatabaseHas('members', [
            'location' => null,
        ]);
    }

    public function testItUpdatesContact(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami();
        $member = $this->member(['nami_id' => null]);
        $this->fakeRequest();

        $response = $this
            ->from("/member/{$member->id}")
            ->patch("/member/{$member->id}", array_merge($member->getAttributes(), [
                'other_country' => 'englisch',
            ]));

        $this->assertEquals('englisch', $member->fresh()->other_country);
    }

    public function testItCreatesMemberWithFirstActivityId(): void
    {
        $this->login()->loginNami()->withoutExceptionHandling();
        $member = Member::factory()->defaults()->create();
        app(MemberFake::class)->stores($member->group->nami_id, 103);
        $activity = Activity::factory()->inNami(89)->create();
        $subactivity = Subactivity::factory()->inNami(90)->create();
        Confession::factory()->create(['is_null' => true]);
        PullMemberAction::shouldRun();
        PullMembershipsAction::shouldRun();

        $this->patch("/member/{$member->id}", [
            ...$member->getAttributes(),
            'has_nami' => true,
            'first_activity_id' => $activity->id,
            'first_subactivity_id' => $subactivity->id,
        ])->assertSessionHasNoErrors();

        app(MemberFake::class)->assertStored($member->group->nami_id, [
            'ersteTaetigkeitId' => 89,
            'ersteUntergliederungId' => 90,
        ]);
    }

    public function testItRequiresFirstActivityId(): void
    {
        $this->login()->loginNami();
        $member = Member::factory()->defaults()->create();
        app(MemberFake::class)->stores($member->group->nami_id, 103);
        Confession::factory()->create(['is_null' => true]);
        PullMemberAction::shouldRun();
        PullMembershipsAction::shouldRun();

        $this->patch("/member/{$member->id}", [
            ...$member->getAttributes(),
            'has_nami' => true,
            'first_activity_id' => null,
            'first_subactivity_id' => null,
        ])->assertSessionHasErrors([
            'first_activity_id' => 'Erste Tätigkeit ist erforderlich.',
            'first_subactivity_id' => 'Erste Untergliederung ist erforderlich.',
        ]);
    }

    public function testItUpdatesCriminalRecord(): void
    {
        $this->withoutExceptionHandling()->login()->loginNami();
        $member = $this->member(['nami_id' => null]);
        $this->fakeRequest();

        $response = $this
            ->from("/member/{$member->id}")
            ->patch("/member/{$member->id}", array_merge($member->getAttributes(), [
                'ps_at' => '2021-02-01',
                'more_ps_at' => '2021-02-02',
                'has_svk' => true,
                'has_vk' => true,
                'efz' => '2021-02-03',
                'without_education_at' => '2021-02-04',
                'without_efz_at' => '2021-02-05',
                'recertified_at' => '2021-02-08',
                'has_nami' => false,
                'multiply_pv' => true,
                'multiply_more_pv' => true,
                'salutation' => 'Doktor',
            ]));

        $this->assertEquals('2021-02-01', $member->fresh()->ps_at->format('Y-m-d'));
        $this->assertEquals('2021-02-02', $member->fresh()->more_ps_at->format('Y-m-d'));
        $this->assertTrue($member->fresh()->has_svk);
        $this->assertTrue($member->fresh()->has_vk);
        $this->assertTrue($member->fresh()->multiply_pv);
        $this->assertTrue($member->fresh()->multiply_more_pv);
        $this->assertEquals('2021-02-03', $member->fresh()->efz->format('Y-m-d'));
        $this->assertEquals('2021-02-04', $member->fresh()->without_education_at->format('Y-m-d'));
        $this->assertEquals('2021-02-05', $member->fresh()->without_efz_at->format('Y-m-d'));
        $this->assertEquals('2021-02-08', $member->fresh()->recertified_at->format('Y-m-d'));
        $this->assertEquals('Doktor', $member->fresh()->salutation);
    }

    /**
     * @param array<string, string|Activity|null> $overwrites
     */
    private function member(array $overwrites = []): Member
    {
        return Member::factory()
            ->for(Group::factory()->state(['nami_id' => 10]))
            ->for(Confession::factory())
            ->for(Nationality::factory())
            ->for(Subscription::factory()->for(Fee::factory()))
            ->for(Country::factory())
            ->create(['nami_id' => 135, ...$overwrites]);
    }

    private function fakeRequest(): void
    {
        Http::fake(function ($request) {
            if ($request->url() === $this->singleMemberUrl(10, 135) && 'GET' === $request->method()) {
                return Http::response('{ "success": true, "data": {"missingkey": "missingvalue", "kontoverbindung": {"a": "b"} } }', 200);
            }

            if ($request->url() === $this->singleMemberUrl(10, 135) && 'PUT' === $request->method() && 43 === $request['version']) {
                return Http::response('{ "success": false, "message": "Update nicht möglich. Der Datensatz wurde zwischenzeitlich verändert." }', 200);
            }

            if ($request->url() === $this->singleMemberUrl(10, 135) && 'PUT' === $request->method()) {
                return Http::response('{ "success": true, "data": { "version": 44 } }', 200);
            }
        });
    }

    private function singleMemberUrl(int $gruppierungId, int $memberId): string
    {
        return "https://nami.dpsg.de/ica/rest/nami/mitglied/filtered-for-navigation/gruppierung/gruppierung/{$gruppierungId}/{$memberId}";
    }
}
