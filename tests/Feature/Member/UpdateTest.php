<?php

namespace Tests\Feature\Member;

use App\Actions\PullMemberAction;
use App\Actions\PullMembershipsAction;
use App\Activity;
use App\Confession;
use App\Group;
use App\Member\Actions\NamiPutMemberAction;
use App\Member\Member;
use App\Subactivity;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Http;
use Tests\RequestFactories\MemberUpdateRequestFactory;
use Zoomyboy\LaravelNami\Fakes\MemberFake;

uses(DatabaseTransactions::class);

function singleMemberUrl(int $gruppierungId, int $memberId): string
{
    return "https://nami.dpsg.de/ica/rest/nami/mitglied/filtered-for-navigation/gruppierung/gruppierung/{$gruppierungId}/{$memberId}";
}

beforeEach(function () {
    Confession::factory()->create(['is_null' => true]);
});

function fakeRequest(): void
{
    Http::fake(function ($request) {
        if ($request->url() === singleMemberUrl(10, 135) && 'GET' === $request->method()) {
            return Http::response('{ "success": true, "data": {"missingkey": "missingvalue", "kontoverbindung": {"a": "b"} } }', 200);
        }

        if ($request->url() === singleMemberUrl(10, 135) && 'PUT' === $request->method() && 43 === $request['version']) {
            return Http::response('{ "success": false, "message": "Update nicht möglich. Der Datensatz wurde zwischenzeitlich verändert." }', 200);
        }

        if ($request->url() === singleMemberUrl(10, 135) && 'PUT' === $request->method()) {
            return Http::response('{ "success": true, "data": { "version": 44 } }', 200);
        }
    });
}

function factory()
{
    return Member::factory()
        ->defaults()
        ->for(Group::factory()->state(['nami_id' => 10]))
        ->state(['nami_id' => 135]);
}

it('calls put action', function () {
    $this->withoutExceptionHandling()->login()->loginNami();
    $member = factory()->create();
    fakeRequest();
    NamiPutMemberAction::allowToRun();

    $this->patch("/member/{$member->id}", MemberUpdateRequestFactory::new()->create());

    NamiPutMemberAction::spy()->shouldHaveReceived('handle')->withArgs(
        fn (Member $memberParam, ?Activity $activityParam, ?Subactivity $subactivityParam) => $memberParam->is($member)
            && null === $activityParam
            && null === $subactivityParam
    )->once();
});

it('redirects to member overview', function () {
    $this->withoutExceptionHandling()->login()->loginNami();
    $member = factory()->create();
    fakeRequest();
    NamiPutMemberAction::allowToRun();

    $this->patch("/member/{$member->id}", MemberUpdateRequestFactory::new()->create())
        ->assertRedirect('/member');
});

it('testItChecksVersion', function () {
    $this->login()->loginNami()->withoutExceptionHandling();
    $member = tap(factory()->create(), fn ($member) => $member->update(['version' => 43]));
    fakeRequest();

    $this->patch("/member/{$member->id}", array_merge($member->getAttributes(), MemberUpdateRequestFactory::new()->create()))
        ->assertRedirect("/member/{$member->id}/edit?conflict=1");
});

it('testItUpdatesPhoneNumber', function () {
    $this->withoutExceptionHandling()->login()->loginNami();
    $member = factory()->create();
    fakeRequest();
    NamiPutMemberAction::allowToRun();

    $this->patch("/member/{$member->id}", MemberUpdateRequestFactory::new()->create([
        'main_phone' => '02103 4455129',
        'fax' => '02103 4455130',
        'children_phone' => '02103 4455130',
    ]));

    $this->assertDatabaseHas('members', [
        'main_phone' => '+49 2103 4455129',
        'fax' => '+49 2103 4455130',
        'children_phone' => '+49 2103 4455130',
    ]);
});

it('testItUpdatesBankAccount', function () {
    $this->withoutExceptionHandling()->login()->loginNami();
    $member = factory()->create();
    fakeRequest();
    NamiPutMemberAction::allowToRun();

    $this->patch("/member/{$member->id}", MemberUpdateRequestFactory::new()->create([
        'bank_account' => [
            'iban' => 'DE1122',
            'bic' => 'SOLSDE',
            'person' => 'Max'
        ]
    ]));

    $this->assertDatabaseHas('bank_accounts', [
        'iban' => 'DE1122',
        'bic' => 'SOLSDE',
        'person' => 'Max',
        'member_id' => $member->id,
    ]);
});

it('testItUpdatesWiederverwendenFlag', function () {
    $this->withoutExceptionHandling()->login()->loginNami();
    $member = factory()->create();
    fakeRequest();
    NamiPutMemberAction::allowToRun();

    $this->patch("/member/{$member->id}", MemberUpdateRequestFactory::new()->create([
        'keepdata' => true,
    ]));

    $this->assertDatabaseHas('members', [
        'keepdata' => true,
    ]);
});

it('testItSetsLocationToNull', function () {
    $this->withoutExceptionHandling()->login()->loginNami();
    $member = factory()->notInNami()->create(['location' => 'Hilden']);
    fakeRequest();
    NamiPutMemberAction::allowToRun();

    $this->patch("/member/{$member->id}", MemberUpdateRequestFactory::new()->noNami()->create([
        'location' => null,
        'bank_account' => []
    ]));

    $this->assertDatabaseHas('members', [
        'location' => null,
    ]);
});

it('updates work phone', function () {
    $this->withoutExceptionHandling()->login()->loginNami();
    $member = factory()->notInNami()->create();
    fakeRequest();
    NamiPutMemberAction::allowToRun();

    $this->patch("/member/{$member->id}", MemberUpdateRequestFactory::new()->noNami()->create([
        'work_phone' => '+49 212 1353688',
    ]));
    test()->assertDatabaseHas('members', ['work_phone' => '+49 212 1353688']);
});

it('testItUpdatesContact', function () {
    $this->withoutExceptionHandling()->login()->loginNami();
    $member = factory()->notInNami()->create();
    fakeRequest();

    $this->patch("/member/{$member->id}", MemberUpdateRequestFactory::new()->noNami()->create([
        'other_country' => 'englisch',
        'bank_account' => []
    ]));

    $this->assertEquals('englisch', $member->fresh()->other_country);
});

it('testItCreatesMemberWithFirstActivityId', function () {
    $this->login()->loginNami()->withoutExceptionHandling();
    $member = factory()->new()->defaults()->create();
    app(MemberFake::class)->stores($member->group->nami_id, 103);
    $activity = Activity::factory()->inNami(89)->create();
    $subactivity = Subactivity::factory()->inNami(90)->create();
    Confession::factory()->create(['is_null' => true]);
    PullMemberAction::shouldRun();
    PullMembershipsAction::shouldRun();

    $this->patch("/member/{$member->id}", MemberUpdateRequestFactory::new()->create([
        'first_activity_id' => $activity->id,
        'first_subactivity_id' => $subactivity->id,
    ]))->assertSessionHasNoErrors();

    app(MemberFake::class)->assertStored($member->group->nami_id, [
        'ersteTaetigkeitId' => 89,
        'ersteUntergliederungId' => 90,
    ]);
});

it('testItRequiresFirstActivityId', function () {
    $this->login()->loginNami();
    $member = factory()->new()->defaults()->create();
    app(MemberFake::class)->stores($member->group->nami_id, 103);
    Confession::factory()->create(['is_null' => true]);
    PullMemberAction::shouldRun();
    PullMembershipsAction::shouldRun();

    $this->patch("/member/{$member->id}", MemberUpdateRequestFactory::new()->create([
        'first_activity_id' => null,
        'first_subactivity_id' => null,
    ]))->assertSessionHasErrors([
        'first_activity_id' => 'Erste Tätigkeit ist erforderlich.',
        'first_subactivity_id' => 'Erste Untergliederung ist erforderlich.',
    ]);
});

it('testItUpdatesCriminalRecord', function () {
    $this->withoutExceptionHandling()->login()->loginNami();
    $member = factory()->notInNami()->create();
    fakeRequest();

    $this
        ->patch("/member/{$member->id}", MemberUpdateRequestFactory::new()->noNami()->create([
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
            'bank_account' => []
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
});
