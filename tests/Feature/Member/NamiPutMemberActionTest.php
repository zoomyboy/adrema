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
use App\Region;
use App\Subactivity;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Phake;
use Zoomyboy\LaravelNami\Fakes\MemberFake;

uses(DatabaseTransactions::class);

it('testItPutsAMember', function (array $memberAttributes, array $storedAttributes) {
    Fee::factory()->create();
    $this->stubIo(PullMemberAction::class, fn ($mock) => $mock);
    $this->stubIo(PullMembershipsAction::class, fn ($mock) => $mock);
    $this->withoutExceptionHandling()->login()->loginNami();
    $country = Country::factory()->create();
    $region = Region::factory()->create();
    $nationality = Nationality::factory()->inNami(565)->create();
    $subscription = Subscription::factory()->forFee()->create();
    $group = Group::factory()->inNami(55)->create();
    $confession = Confession::factory()->inNami(567)->create(['is_null' => true]);
    app(MemberFake::class)->stores(55, 993);
    $activity = Activity::factory()->hasAttached(Subactivity::factory()->name('Biber')->inNami(55))->name('Leiter')->inNami(6)->create();
    $subactivity = $activity->subactivities->first();

    $member = Member::factory()
        ->for($country)
        ->for($subscription)
        ->for($region)
        ->for($nationality)
        ->for($group)
        ->emailBillKind()
        ->create($memberAttributes);

    NamiPutMemberAction::run($member, $activity, $subactivity);

    app(MemberFake::class)->assertStored(55, [
        'ersteTaetigkeitId' => 6,
        'ersteUntergliederungId' => 55,
        'konfessionId' => 567,
        ...$storedAttributes,
    ]);
    $this->assertDatabaseHas('members', [
        'nami_id' => 993,
    ]);
    Phake::verify(app(PullMemberAction::class))->handle(55, 993);
    Phake::verify(app(PullMembershipsAction::class))->handle($member);
})->with([
    [
        ['email_parents' => 'a@b.de'], ['emailVertretungsberechtigter' => 'a@b.de'],
    ],
    [
        ['keepdata' => true], ['wiederverwendenFlag' => true],
    ],
    [
        ['keepdata' => false], ['wiederverwendenFlag' => false],
    ],
]);
