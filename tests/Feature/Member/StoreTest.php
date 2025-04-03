<?php

namespace Tests\Feature\Member;

use App\Actions\PullMemberAction;
use App\Actions\PullMembershipsAction;
use App\Activity;
use App\Confession;
use App\Country;
use App\Fee;
use App\Gender;
use App\Member\Actions\NamiPutMemberAction;
use App\Member\Member;
use App\Nationality;
use App\Payment\Subscription;
use App\Region;
use App\Subactivity;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Lib\MergesAttributes;
use Tests\RequestFactories\MemberStoreRequestFactory;
use Tests\TestCase;
use Zoomyboy\LaravelNami\Fakes\MemberFake;

uses(DatabaseTransactions::class);

beforeEach(function () {
    Confession::factory()->create(['is_null' => true]);
    PullMemberAction::shouldRun();
    PullMembershipsAction::shouldRun();
});

it('can store a member', function () {
    app(MemberFake::class)->stores(55, 103);
    Fee::factory()->create();
    $this->withoutExceptionHandling()->login()->loginNami();
    $country = Country::factory()->create();
    $gender = Gender::factory()->create();
    $region = Region::factory()->create();
    $nationality = Nationality::factory()->create();
    $activity = Activity::factory()->inNami(89)->create();
    $subactivity = Subactivity::factory()->inNami(90)->create();
    $subscription = Subscription::factory()->forFee()->create();

    $response = $this
        ->from('/member/create')
        ->post('/member', MemberStoreRequestFactory::new()->create([
            'country_id' => $country->id,
            'gender_id' => $gender->id,
            'region_id' => $region->id,
            'nationality_id' => $nationality->id,
            'first_activity_id' => $activity->id,
            'first_subactivity_id' => $subactivity->id,
            'subscription_id' => $subscription->id,
            'bill_kind' => 'Post',
            'salutation' => 'Doktor',
            'comment' => 'Lorem bla',
        ]))->assertSessionHasNoErrors();

    $response->assertRedirect('/member')->assertSessionHasNoErrors();
    $this->assertDatabaseHas('members', [
        'address' => 'Bavert 50',
        'bill_kind' => 'Post',
        'birthday' => '2013-02-19',
        'children_phone' => '+49 176 70512778',
        'country_id' => $country->id,
        'email_parents' => 'osloot@aol.com',
        'firstname' => 'Joe',
        'gender_id' => $gender->id,
        'joined_at' => '2022-08-12',
        'lastname' => 'Muster',
        'letter_address' => null,
        'location' => 'Solingen',
        'main_phone' => '+49 212 337056',
        'mobile_phone' => '+49 176 70512774',
        'nationality_id' => $nationality->id,
        'region_id' => $region->id,
        'send_newspaper' => '1',
        'subscription_id' => $subscription->id,
        'zip' => '42719',
        'fax' => '+49 212 4732223',
        'salutation' => 'Doktor',
        'comment' => 'Lorem bla',
    ]);

    app(MemberFake::class)->assertStored(55, [
        'ersteTaetigkeitId' => 89,
        'ersteUntergliederungId' => 90,
    ]);
});

it('can store iban and bic', function () {
    app(MemberFake::class)->stores(55, 103);
    Fee::factory()->create();
    $this->withoutExceptionHandling()->login()->loginNami();

    $this->post('/member', MemberStoreRequestFactory::new()->create([
        'bank_account.iban' => '666',
        'bank_account.bic' => 'SOLSDE',
    ]))->assertSessionHasNoErrors();

    $this->assertDatabaseHas('bank_accounts', [
        'iban' => '666',
        'bic' => 'SOLSDE',
        'member_id' => Member::first()->id,
    ]);

    app(MemberFake::class)->assertStored(55, function ($payload) {
        $bank = json_decode($payload['kontoverbindung'], true);
        return $bank['iban'] === '666' && $bank['bic'] === 'SOLSDE';
    });
});

it('testItStoresWiederverwendenFlag', function () {
    app(MemberFake::class)->stores(55, 103);
    Fee::factory()->create();
    $this->withoutExceptionHandling()->login()->loginNami();
    $activity = Activity::factory()->inNami(89)->create();
    $subactivity = Subactivity::factory()->inNami(90)->create();
    $subscription = Subscription::factory()->forFee()->create();

    $this
        ->from('/member/create')
        ->post('/member', MemberStoreRequestFactory::new()->create([
            'first_activity_id' => $activity->id,
            'first_subactivity_id' => $subactivity->id,
            'subscription_id' => $subscription->id,
            'keepdata' => true,
        ]))->assertSessionHasNoErrors();

    $this->assertDatabaseHas('members', [
        'keepdata' => true,
    ]);
    app(MemberFake::class)->assertStored(55, [
        'wiederverwendenFlag' => true,
    ]);
});

it('testItCanStoreAMemberWithoutNami', function () {
    $this->withoutExceptionHandling()->login()->loginNami();
    $activity = Activity::factory()->create();
    $subactivity = Subactivity::factory()->create();

    $response = $this
        ->from('/member/create')
        ->post('/member', MemberStoreRequestFactory::new()->create([
            'first_activity_id' => $activity->id,
            'first_subactivity_id' => $subactivity->id,
            'has_nami' => false,
        ]));

    $response->assertSessionHasNoErrors();
    $this->assertDatabaseHas('members', [
        'nami_id' => null,
    ]);
    NamiPutMemberAction::spy()->shouldNotHaveReceived('handle');
});

it('testItUpdatesPhoneNumber', function () {
    $this->withoutExceptionHandling()->login()->loginNami();

    $this->post('/member', MemberStoreRequestFactory::new()->create([
        'has_nami' => false,
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

it('testItHasErrorWhenPhoneNumberIsInvalid', function () {
    $this->login()->loginNami();

    $response = $this->post('/member', MemberStoreRequestFactory::new()->create([
        'has_nami' => false,
        'main_phone' => '1111111111111111',
        'mobile_phone' => '1111111111111111',
        'fax' => '1111111111111111',
        'children_phone' => '1111111111111111',
    ]));

    $response->assertSessionHasErrors([
        'main_phone' => 'Telefon (Eltern) ist keine valide Nummer.',
        'mobile_phone' => 'Handy (Eltern) ist keine valide Nummer.',
        'children_phone' => 'Telefon (Kind) ist keine valide Nummer.',
        'fax' => 'Fax ist keine valide Nummer.',
    ]);
});

it('testItDoesntRequireBirthdayWhenNotInNami', function () {
    $this->login()->loginNami();

    $this
        ->post('/member', MemberStoreRequestFactory::new()->create([
            'nationality_id' => null,
            'birthday' => null,
            'has_nami' => false,
            'address' => null,
            'zip' => null,
            'location' => null,
            'joined_at' => null,
        ]))->assertSessionDoesntHaveErrors();
    $this->assertDatabaseHas('members', [
        'nationality_id' => null,
        'birthday' => null,
        'address' => null,
        'zip' => null,
        'location' => null,
        'joined_at' => null,
    ]);
});

it('testItDoesntNeedSubscription', function () {
    $this->login()->loginNami();

    $this
        ->post('/member', MemberStoreRequestFactory::new()->create([
            'has_nami' => false,
            'subscription_id' => null,
        ]))->assertSessionDoesntHaveErrors();
    $this->assertDatabaseHas('members', [
        'subscription_id' => null,
    ]);
});

it('testItRequiresFields', function () {
    $this->login()->loginNami();

    $this
        ->post('/member', MemberStoreRequestFactory::new()->create([
            'nationality_id' => null,
            'birthday' => '',
            'address' => '',
            'zip' => '',
            'location' => '',
            'joined_at' => '',
        ]))
        ->assertSessionHasErrors(['nationality_id', 'birthday', 'address', 'zip', 'location', 'joined_at']);
});

it('testSubscriptionIsRequiredIfFirstActivityIsPaid', function () {
    $this->login()->loginNami();
    $activity = Activity::factory()->name('€ Mitglied')->create();
    $subactivity = Subactivity::factory()->create();

    $this
        ->from('/member/create')
        ->post('/member', MemberStoreRequestFactory::new()->create([
            'first_activity_id' => $activity->id,
            'first_subactivity_id' => $subactivity->id,
            'subscription_id' => null,
        ]))
        ->assertSessionHasErrors(['subscription_id' => 'Beitragsart ist erforderlich.']);
});
