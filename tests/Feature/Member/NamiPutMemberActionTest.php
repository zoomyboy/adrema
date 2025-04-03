<?php

namespace Tests\Feature\Member;

use App\Actions\PullMemberAction;
use App\Actions\PullMembershipsAction;
use App\Activity;
use App\Confession;
use App\Country;
use App\Gender;
use App\Group;
use App\Member\Actions\NamiPutMemberAction;
use App\Member\BankAccount;
use App\Member\Member;
use App\Payment\Subscription;
use App\Region;
use App\Subactivity;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Phake;
use Zoomyboy\LaravelNami\Fakes\MemberFake;

uses(DatabaseTransactions::class);
covers(NamiPutMemberAction::class);

beforeEach(function () {
    $this->stubIo(PullMemberAction::class, fn ($mock) => $mock);
    $this->stubIo(PullMembershipsAction::class, fn ($mock) => $mock);
    Group::factory()->inNami(55)->create();
    $this->withoutExceptionHandling()->login()->loginNami();
    app(MemberFake::class)->stores(55, 993);
    Confession::factory()->inNami(567)->create(['is_null' => true]);
    $activity = Activity::factory()->inNami(6)->create();
    Subactivity::factory()->hasAttached($activity)->inNami(55)->create();
});

it('pulls member and memberships befre pushing', function () {
    $member = Member::factory()->defaults()->create();

    NamiPutMemberAction::run($member, Activity::first(), Subactivity::first());

    Phake::verify(app(PullMemberAction::class))->handle(55, 993);
    Phake::verify(app(PullMembershipsAction::class))->handle($member);
});

it('sets nami id of member', function () {
    $member = Member::factory()->defaults()->create();

    NamiPutMemberAction::run($member, Activity::first(), Subactivity::first());

    $this->assertDatabaseHas('members', ['nami_id' => 993]);
});

it('stores member attributes', function (array $memberAttributes, array $storedAttributes) {
    $member = Member::factory()->defaults()->create($memberAttributes);

    NamiPutMemberAction::run($member, Activity::first(), Subactivity::first());

    app(MemberFake::class)->assertStored(55, $storedAttributes);
})->with([
    [['firstname' => 'Phi'], ['vorname' => 'Phi']],
    [['lastname' => 'Phi'], ['nachname' => 'Phi']],
    [['nickname' => 'Nick'], ['spitzname' => 'Nick']],
    [['email' => 'a@b.de'], ['email' => 'a@b.de']],
    [['zip' => '5566'], ['plz' => '5566']],
    [['location' => 'SG'], ['ort' => 'SG']],
    [['further_address' => 'SG'], ['nameZusatz' => 'SG']],
    [['other_country' => 'SG'], ['staatsangehoerigkeitText' => 'SG']],
    [['address' => 'Add'], ['strasse' => 'Add']],
    [['main_phone' => '+49 212 5566234'], ['telefon1' => '+49 212 5566234']],
    [['mobile_phone' => '+49 212 5566234'], ['telefon2' => '+49 212 5566234']],
    [['work_phone' => '+49 212 5566234'], ['telefon3' => '+49 212 5566234']],
    [['email_parents' => 'a@b.de'], ['emailVertretungsberechtigter' => 'a@b.de']],
    [['keepdata' => true], ['wiederverwendenFlag' => true]],
    [['keepdata' => false], ['wiederverwendenFlag' => false]],
    fn () => [['joined_at' => now()], ['eintrittsdatum' => now()->format('Y-m-d') . ' 00:00:00']],
    [['fax' => '555'], ['telefax' => '555']],
    [[], ['konfessionId' => 567]],
    [[], ['ersteTaetigkeitId' => 6]],
    [[], ['ersteUntergliederungId' => 55]],
]);

it('stores related models', function () {
    Subscription::factory()->forFee(3)->create();
    $member = Member::factory()->defaults()
        ->for(Country::factory()->inNami(1)->create())
        ->for(Region::factory()->inNami(2)->create())
        ->for(Gender::factory()->inNami(4)->create())
        ->create();

    NamiPutMemberAction::run($member, Activity::first(), Subactivity::first());

    app(MemberFake::class)->assertStored(55, ['regionId' => 2, 'landId' => 1, 'beitragsartId' => 3, 'geschlechtId' => 4]);
});

it('stores bank account with empty values', function () {
    $member = Member::factory()->defaults()->create(['mitgliedsnr' => 56]);

    NamiPutMemberAction::run($member, Activity::first(), Subactivity::first());

    app(MemberFake::class)->assertStored(55, ['kontoverbindung' => json_encode([
        'id' => '',
        'zahlungsKonditionId' => null,
        'mitgliedsNummer' => 56,
        'institut' => '',
        'kontoinhaber' => '',
        'kontonummer' => '',
        'bankleitzahl' => '',
        'iban' => '',
        'bic' => ''
    ])]);
});

it('updates existing member', function () {
    app(MemberFake::class)->updatesSuccessfully(55, 103)->shows(55, 103);
    $member = Member::factory()->defaults()->inNami(103)->create(['version' => 50]);

    NamiPutMemberAction::run($member, Activity::first(), Subactivity::first());

    app(MemberFake::class)->assertUpdated(55, 103, ['id' => 103, 'version' => 50]);
});

it('updates bank account with filled values', function () {
    app(MemberFake::class)->updatesSuccessfully(55, 103)->shows(55, 103);
    $member = Member::factory()->defaults()
        ->withBankAccount(BankAccount::factory()->inNami(30)->state([
            'bank_name' => 'Stadt',
            'bic' => 'SOLSDE33',
            'iban' => 'DE50',
            'blz' => 'ssss',
            'person' => 'Pill',
            'account_number' => 'ddf',
        ]))
        ->inNami(103)
        ->create(['mitgliedsnr' => 56]);

    NamiPutMemberAction::run($member, Activity::first(), Subactivity::first());

    app(MemberFake::class)->assertUpdated(55, 103, ['kontoverbindung' => json_encode([
        'id' => 30,
        'zahlungsKonditionId' => null,
        'mitgliedsNummer' => 56,
        'institut' => 'Stadt',
        'kontoinhaber' => 'Pill',
        'kontonummer' => 'ddf',
        'bankleitzahl' => 'ssss',
        'iban' => 'DE50',
        'bic' => 'SOLSDE33'
    ])]);
});
