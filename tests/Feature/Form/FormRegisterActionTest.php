<?php

namespace Tests\Feature\Form;

use App\Form\Enums\NamiType;
use App\Form\Enums\SpecialType;
use App\Form\FormSettings;
use App\Form\Mails\ConfirmRegistrationMail;
use App\Form\Models\Form;
use App\Group;
use App\Group\Enums\Level;
use Carbon\Carbon;
use Database\Factories\Member\MemberFactory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Tests\Lib\CreatesFormFields;

uses(DatabaseTransactions::class);
uses(CreatesFormFields::class);

beforeEach(function () {
    test()->setUpForm();
    Mail::fake();
});

dataset('validation', fn() => [
    fn () => [
        test()->dateField('birthday')->name('Geburtsdatum')->maxToday(false),
        ['birthday' => 'aa'],
        ['birthday' => 'Geburtsdatum muss ein gültiges Datum sein.']
    ],

    fn () => [
        test()->dateField('birthday')->name('Geburtsdatum')->maxToday(false),
        ['birthday' => '2021-05-06'],
        null,
    ],

    fn () => [
        test()->dateField('birthday')->name('Geburtsdatum')->maxToday(true),
        ['birthday' => '2024-02-16'],
        ['birthday' => 'Geburtsdatum muss ein Datum vor oder gleich dem 15.02.2024 sein.'],
    ],

    fn () => [
        test()->dateField('birthday')->name('Geburtsdatum')->maxToday(true),
        ['birthday' => '2024-02-15'],
        null,
    ],

    fn () => [
        test()->textField('vorname')->name('Vorname der Mutter')->required(true),
        ['vorname' => ''],
        ['vorname' => 'Vorname der Mutter ist erforderlich.']
    ],

    fn () => [
        test()->textField('vorname')->name('Vorname der Mutter')->required(true),
        ['vorname' => 5],
        ['vorname' => 'Vorname der Mutter muss ein String sein.']
    ],

    fn () => [
        test()->radioField('yes_or_no')->name('Ja oder Nein')->required(true),
        ['yes_or_no' => null],
        ['yes_or_no' => 'Ja oder Nein ist erforderlich.']
    ],

    fn () => [
        test()->radioField('letter')->name('Buchstabe')->options(['A', 'B'])->required(false)->allowcustom(false),
        ['letter' => 'Z'],
        ['letter' => 'Der gewählte Wert für Buchstabe ist ungültig.']
    ],

    fn () => [
        test()->radioField('letter')->name('Buchstabe')->options(['A', 'B'])->required(true)->allowcustom(false),
        ['letter' => 'Z'],
        ['letter' => 'Der gewählte Wert für Buchstabe ist ungültig.']
    ],

    fn () => [
        test()->radioField('letter')->name('Buchstabe')->options(['A', 'B'])->required(true)->allowcustom(true),
        ['letter' => 'lalalaa'],
        null,
    ],

    fn () => [
        test()->radioField('letter')->name('Buchstabe')->options(['A', 'B'])->required(true)->allowcustom(false),
        ['letter' => 'A'],
        null
    ],

    fn () => [
        test()->checkboxesField('letter')->name('Buchstabe')->options(['A', 'B']),
        ['letter' => ['Z']],
        ['letter.0' => 'Der gewählte Wert für Buchstabe ist ungültig.'],
    ],

    fn () => [
        test()->dropdownField('letter')->name('Buchstabe')->options(['A', 'B'])->allowcustom(true),
        ['letter' => 'Z'],
        null,
    ],

    fn () => [
        test()->checkboxesField('letter')->name('Buchstabe')->options(['A', 'B']),
        ['letter' => 77],
        ['letter' => 'Buchstabe muss ein Array sein.'],
    ],

    fn () => [
        test()->checkboxesField('letter')->name('Buchstabe')->options(['A', 'B']),
        ['letter' => ['A']],
        null,
    ],

    fn () => [
        test()->checkboxesField('letter')->name('Buchstabe')->options(['A', 'B']),
        ['letter' => []],
        null,
    ],

    fn () => [
        test()->checkboxesField('letter')->name('Buchstabe')->options(['A', 'B', 'C', 'D'])->min(0)->max(2),
        ['letter' => ['A', 'B', 'C']],
        ['letter' => 'Buchstabe darf maximal 2 Elemente haben.'],
    ],

    fn () => [
        test()->checkboxesField('letter')->name('Buchstabe')->options(['A', 'B', 'C', 'D'])->min(2)->max(0),
        ['letter' => ['A']],
        ['letter' => 'Buchstabe muss mindestens 2 Elemente haben.'],
    ],

    fn () => [
        test()->checkboxesField('letter')->name('Buchstabe')->options(['A', 'B', 'C', 'D'])->min(1)->max(0),
        ['letter' => []],
        ['letter' => 'Buchstabe muss mindestens 1 Elemente haben.'],
    ],

    fn () => [
        test()->checkboxesField('letter')->name('Buchstabe')->options(['A', 'B', 'C', 'D'])->min(0)->max(1),
        ['letter' => ['A', 'B']],
        ['letter' => 'Buchstabe darf maximal 1 Elemente haben.'],
    ],

    fn () => [
        test()->checkboxField('data')->name('Datenschutz')->required(false),
        ['data' => 5],
        ['data' => 'Datenschutz muss ein Wahrheitswert sein.'],
    ],

    fn () => [
        test()->checkboxField('data')->name('Datenschutz')->required(false),
        ['data' => false],
        null
    ],

    fn () => [
        test()->checkboxField('data')->name('Datenschutz')->required(true),
        ['data' => false],
        ['data' => 'Datenschutz muss akzeptiert werden.'],
    ],

    fn () => [
        test()->checkboxField('data')->name('Datenschutz')->required(true),
        ['data' => true],
        null,
    ],

    fn () => [
        test()->dropdownField('yes_or_no')->name('Ja oder Nein')->required(true),
        ['yes_or_no' => null],
        ['yes_or_no' => 'Ja oder Nein ist erforderlich.']
    ],

    fn () => [
        test()->dropdownField('letter')->name('Buchstabe')->options(['A', 'B'])->required(false)->allowcustom(false),
        ['letter' => 'Z'],
        ['letter' => 'Der gewählte Wert für Buchstabe ist ungültig.']
    ],

    fn () => [
        test()->dropdownField('letter')->name('Buchstabe')->options(['A', 'B'])->required(true)->allowcustom(false),
        ['letter' => 'Z'],
        ['letter' => 'Der gewählte Wert für Buchstabe ist ungültig.']
    ],

    fn () => [
        test()->dropdownField('letter')->name('Buchstabe')->options(['A', 'B'])->required(true)->allowcustom(false),
        ['letter' => 'A'],
        null
    ],

    fn () => [
        test()->textareaField('vorname')->name('Vorname der Mutter')->required(true),
        ['vorname' => ''],
        ['vorname' => 'Vorname der Mutter ist erforderlich.']
    ],

    fn () => [
        test()->textareaField('vorname')->name('Vorname der Mutter')->required(true),
        ['vorname' => 5],
        ['vorname' => 'Vorname der Mutter muss ein String sein.']
    ],

    fn () => [
        test()->textareaField('vorname')->name('Vorname der Mutter')->required(true),
        ['vorname' => 5],
        ['vorname' => 'Vorname der Mutter muss ein String sein.']
    ],

    fn () => [
        test()->emailField('email')->name('Mail')->required(true),
        ['email' => 'alaaa'],
        ['email' => 'Mail muss eine gültige E-Mail-Adresse sein.']
    ],

    fn () => [
        test()->emailField('email')->name('Mail')->required(false),
        ['email' => 'alaaa'],
        ['email' => 'Mail muss eine gültige E-Mail-Adresse sein.']
    ],

    fn () => [
        test()->numberField('numb')->name('Nummer')->required(false)->min(10)->max(20),
        ['numb' => 21],
        ['numb' => 'Nummer muss kleiner oder gleich 20 sein.']
    ],

    fn () => [
        test()->numberField('numb')->name('Nummer')->required(false)->min(10)->max(20),
        ['numb' => 9],
        ['numb' => 'Nummer muss größer oder gleich 10 sein.']
    ],

    fn () => [
        test()->numberField('numb')->name('Nummer')->required(false)->min(10)->max(20),
        ['numb' => 'asss'],
        ['numb' => 'Nummer muss eine ganze Zahl sein.']
    ],

    fn () => [
        test()->numberField('numb')->name('Nummer')->required(true),
        ['numb' => ''],
        ['numb' => 'Nummer ist erforderlich.']
    ]
]);

it('testItSavesParticipantAsModel', function () {
    $this->login()->loginNami()->withoutExceptionHandling();
    $form = Form::factory()
        ->sections([
            FormtemplateSectionRequest::new()->fields([
                $this->textField('vorname'),
                $this->textField('nachname'),
            ]),
            FormtemplateSectionRequest::new()->fields([
                $this->textField('spitzname'),
            ]),
        ])
        ->create();

    $this->register($form, ['vorname' => 'Max', 'nachname' => 'Muster', 'spitzname' => 'Abraham'])
        ->assertOk();

    $participants = $form->fresh()->participants;
    $this->assertCount(1, $participants);
    $this->assertEquals('Max', $participants->first()->data['vorname']);
    $this->assertEquals('Muster', $participants->first()->data['nachname']);
    $this->assertEquals('Abraham', $participants->first()->data['spitzname']);
});


it('cannot register when event is inactive', function () {
    $this->login()->loginNami();
    $form = Form::factory()->isActive(false)->create();

    $this->register($form, [])->assertJsonValidationErrors(['event' => 'Anmeldung zzt nicht möglich.']);
});

it('testItCannotRegisterWhenRegistrationFromReached', function () {
    $this->login()->loginNami();
    $form = Form::factory()->registrationFrom(now()->addDay())->create();

    $this->register($form, [])->assertJsonValidationErrors(['event' => 'Anmeldung zzt nicht möglich.']);
});

it('testItCannotRegisterWhenRegistrationUntilReached', function () {
    $this->login()->loginNami();
    $form = Form::factory()->registrationUntil(now()->subDay())->create();

    $this->register($form, [])->assertJsonValidationErrors(['event' => 'Anmeldung zzt nicht möglich.']);
});

it('testItSendsEmailToParticipant', function () {
    $this->login()->loginNami()->withoutExceptionHandling();
    $form = Form::factory()->name('Ver2')->fields([
        $this->textField('vorname')->specialType(SpecialType::FIRSTNAME),
        $this->textField('nachname')->specialType(SpecialType::LASTNAME),
        $this->textField('email')->specialType(SpecialType::EMAIL),
    ])
        ->create();

    $this->register($form, ['vorname' => 'Lala', 'nachname' => 'GG', 'email' => 'example@test.test'])
        ->assertOk();

    Mail::assertQueued(ConfirmRegistrationMail::class, fn($message) => $message->hasTo('example@test.test', 'Lala GG') && $message->hasSubject('Deine Anmeldung zu Ver2'));
});

it('sets reply to in email', function () {
    $this->login()->loginNami()->withoutExceptionHandling();
    app(FormSettings::class)->fill(['replyToMail' => 'reply@example.com'])->save();
    $form = Form::factory()->name('Ver2')->fields([
        $this->textField('vorname')->specialType(SpecialType::FIRSTNAME),
        $this->textField('nachname')->specialType(SpecialType::LASTNAME),
        $this->textField('email')->specialType(SpecialType::EMAIL),
    ])
        ->create();

    $this->register($form, ['vorname' => 'Lala', 'nachname' => 'GG', 'email' => 'example@test.test'])
        ->assertOk();

    Mail::assertQueued(ConfirmRegistrationMail::class, fn($message) => $message->hasReplyTo('reply@example.com'));
});

it('testItDoesntSendEmailWhenNoMailFieldGiven', function () {
    $this->login()->loginNami()->withoutExceptionHandling();
    $form = Form::factory()->fields([
        $this->textField('vorname')->specialType(SpecialType::FIRSTNAME),
        $this->textField('nachname')->specialType(SpecialType::LASTNAME),
    ])
        ->create();

    $this->register($form, ['vorname' => 'Lala', 'nachname' => 'GG'])
        ->assertOk();

    Mail::assertNotQueued(ConfirmRegistrationMail::class);
});

/**
 * @param array<string, mixed> $payload
 * @param ?array<string, mixed> $messages
 */
it('testItValidatesInput', function (FormtemplateFieldRequest $fieldGenerator, array $payload, ?array $messages) {
    Carbon::setTestNow(Carbon::parse('2024-02-15 06:00:00'));
    $this->login()->loginNami();
    $form = Form::factory()->fields([$fieldGenerator])->create();

    $response = $this->postJson(route('form.register', ['form' => $form]), $payload);

    if ($messages) {
        $response->assertJsonValidationErrors($messages);
    } else {
        $response->assertOk();
    }
})->with('validation');

it('testItValidatesGroupFieldWithParentGroupField', function () {
    $this->login()->loginNami();
    $group = Group::factory()->has(Group::factory()->count(3), 'children')->create();
    $foreignGroup = Group::factory()->create();
    $form = Form::factory()->fields([
        $this->groupField('group')->name('Gruppe')->parentGroup($group->id)->required(true)
    ])
        ->create();

    $this->register($form, ['group' => null])
        ->assertJsonValidationErrors(['group' => 'Gruppe ist erforderlich.']);
    $this->register($form, ['group' => $foreignGroup->id])
        ->assertJsonValidationErrors(['group' => 'Der gewählte Wert für Gruppe ist ungültig.']);
});

it('testGroupFieldCanBeUnsetWhenGiven', function () {
    $this->login()->loginNami();
    $group = Group::factory()->has(Group::factory(), 'children')->create();
    $form = Form::factory()->fields([
        $this->groupField('region')->emptyOptionValue('kein Bezirk')->parentGroup($group->id)->required(false),
        $this->groupField('stamm')->name('Gruppe')->emptyOptionValue('kein Stamm')->parentField('region')->required(true)
    ])
        ->create();

    $this->register($form, ['region' => -1, 'stamm' => -1])->assertOk();
    $participants = $form->fresh()->participants;
    $this->assertEquals(-1, $participants->first()->data['region']);
    $this->assertEquals(-1, $participants->first()->data['stamm']);
});

it('testGroupFieldCanBeNullWhenNotRequired', function () {
    $this->login()->loginNami();
    $form = Form::factory()->fields([
        $this->groupField('group')->parentGroup(Group::factory()->create()->id)->required(false)
    ])
        ->create();

    $this->register($form, ['group' => null])
        ->assertOk();
});

it('testItValidatesGroupWithParentFieldField', function () {
    $this->login()->loginNami();
    $group = Group::factory()->has(Group::factory()->has(Group::factory()->count(3), 'children'), 'children')->create();
    $foreignGroup = Group::factory()->create();
    $form = Form::factory()->fields([
        $this->groupField('parentgroup')->name('Übergeordnete Gruppe')->parentGroup($group->id)->required(true),
        $this->groupField('group')->name('Gruppe')->parentField('parentgroup')->required(true),
    ])
        ->create();

    $this->register($form, ['parentgroup' => $group->children->first()->id, 'group' => $foreignGroup->id])
        ->assertJsonValidationErrors(['group' => 'Der gewählte Wert für Gruppe ist ungültig.']);
    $this->register($form, ['parentgroup' => $group->children->first()->id, 'group' => $group->children->first()->children->first()->id])
        ->assertOk();
});

it('testItSetsMitgliedsnrForMainMember', function () {
    $this->login()->loginNami();
    $member = $this->createMember(['mitgliedsnr' => '9966', 'email' => 'max@muster.de', 'firstname' => 'Max', 'lastname' => 'Muster']);
    $form = Form::factory()->fields([
        $this->textField('email')->namiType(NamiType::EMAIL),
        $this->textField('firstname')->namiType(NamiType::FIRSTNAME),
        $this->textField('lastname')->namiType(NamiType::LASTNAME),
    ])
        ->create();

    $this->register($form, ['email' => 'max@muster.de', 'firstname' => 'Max', 'lastname' => 'Muster'])->assertOk();
    $this->assertEquals($member->id, $form->participants->first()->member_id);
});

it('testItDoesntSetMitgliedsnrWhenFieldDoesntHaveType', function () {
    $this->login()->loginNami();
    $this->createMember(['mitgliedsnr' => '9966', 'email' => 'max@muster.de']);
    $form = Form::factory()->fields([
        $this->textField('email'),
    ])
        ->create();

    $this->register($form, ['email' => 'max@muster.de'])->assertOk();
    $this->assertNull($form->participants->first()->member_id);
});

it('testItDoesntSyncMembersWhenTwoMembersMatch', function () {
    $this->login()->loginNami();
    $this->createMember(['mitgliedsnr' => '9966', 'email' => 'max@muster.de']);
    $this->createMember(['mitgliedsnr' => '9967', 'email' => 'max@muster.de']);
    $form = Form::factory()->fields([
        $this->textField('email')->namiType(NamiType::EMAIL),
    ])
        ->create();

    $this->register($form, ['email' => 'max@muster.de'])->assertOk();
    $this->assertNull($form->participants->first()->member_id);
    $this->assertNull($form->participants->first()->parent_id);
});

// --------------------------- NamiField Tests ---------------------------
// ***********************************************************************
it('testItAddsMitgliedsnrFromMembers', function () {
    $this->login()->loginNami();
    $this->createMember(['mitgliedsnr' => '5505']);
    $this->createMember(['mitgliedsnr' => '5506']);
    $form = Form::factory()->fields([
        $this->namiField('members'),
    ])
        ->create();

    $this->register($form, ['members' => [['id' => '5505'], ['id' => '5506']]])
        ->assertOk();
    $this->assertCount(3, $form->participants()->get());
    $this->assertEquals([['id' => '5505'], ['id' => '5506']], $form->participants->get(0)->data['members']);
    $this->assertEquals([], $form->participants->get(1)->data['members']);
    $this->assertEquals([], $form->participants->get(2)->data['members']);
    $this->assertEquals($form->participants->get(0)->id, $form->participants->get(2)->parent_id);
    $this->assertEquals($form->participants->get(0)->id, $form->participants->get(1)->parent_id);
});

/**
 * @param array<string, string> $memberAttributes
 * @param mixed $participantValue
 */
it('testItSynchsMemberAttributes', function (array $memberAttributes, NamiType $type, mixed $participantValue, ?callable $factory = null) {
    Carbon::setTestNow(Carbon::parse('2023-05-04'));
    $this->login()->loginNami();
    $this->createMember(['mitgliedsnr' => '5505', ...$memberAttributes], $factory);
    $form = Form::factory()->fields([
        $this->namiField('members'),
        $this->textField('other')->required(true)->namiType($type),
    ])
        ->from('2023-08-15')
        ->create();

    $this->register($form, ['other' => '::other::', 'members' => [['id' => '5505']]])->assertOk();
    $this->assertEquals($participantValue, $form->participants->get(1)->data['other']);
})->with([
    [
        ['email' => 'max@muster.de'],
        NamiType::EMAIL,
        'max@muster.de',
    ],

    [
        ['firstname' => 'Philipp'],
        NamiType::FIRSTNAME,
        'Philipp'
    ],

    [
        ['lastname' => 'Muster'],
        NamiType::LASTNAME,
        'Muster'
    ],

    [
        ['address' => 'Maxstr 5'],
        NamiType::ADDRESS,
        'Maxstr 5'
    ],

    [
        ['zip' => 44444],
        NamiType::ZIP,
        '44444'
    ],

    [
        ['location' => 'Hilden'],
        NamiType::LOCATION,
        'Hilden'
    ],

    [
        ['birthday' => '2023-06-06'],
        NamiType::BIRTHDAY,
        '2023-06-06'
    ],

    [
        [],
        NamiType::GENDER,
        'Männlich',
        fn(MemberFactory $factory) => $factory->male(),
    ],

    [
        ['gender_id' => null],
        NamiType::GENDER,
        '',
    ],

    [
        ['birthday' => '1991-10-02'],
        NamiType::AGE,
        '31'
    ],

    [
        ['birthday' => '1991-05-04'],
        NamiType::AGE,
        '32'
    ],

    [
        ['birthday' => '1991-08-15'],
        NamiType::AGEEVENT,
        '32'
    ],

    [
        ['mobile_phone' => '+49 7776666'],
        NamiType::MOBILEPHONE,
        '+49 7776666'
    ],
]);

it('testItAddsOtherFieldsOfMember', function () {
    $this->login()->loginNami();
    $this->createMember(['mitgliedsnr' => '5505']);
    $form = Form::factory()->fields([
        $this->namiField('members'),
        $this->textField('other')->required(false),
    ])
        ->create();

    $this->register($form, ['other' => '::string::', 'members' => [['id' => '5505', 'other' => 'othervalue']]])
        ->assertOk();
    $this->assertEquals('othervalue', $form->participants->get(1)->data['other']);
});

it('testItAddsMemberForNonNami', function () {
    $this->login()->loginNami();
    $this->createMember(['mitgliedsnr' => null]);
    $form = Form::factory()->fields([
        $this->namiField('members'),
        $this->textField('gender')->namiType(NamiType::GENDER)->required(false),
        $this->textField('vorname')->namiType(NamiType::FIRSTNAME)->required(false),
        $this->textField('other')->required(false),
    ])
        ->create();

    $this->register($form, ['other' => '::string::', 'vorname' => 'LA', 'members' => [['id' => null, 'vorname' => 'BBB', 'gender' => 'Herr', 'other' => 'othervalue']]])
        ->assertOk();
    $this->assertEquals('othervalue', $form->participants->get(1)->data['other']);
    $this->assertEquals('Herr', $form->participants->get(1)->data['gender']);
    $this->assertEquals('BBB', $form->participants->get(1)->data['vorname']);
});

it('testItValidatesNamiTypeFieldsForNonMembers', function () {
    $this->login()->loginNami();
    $form = Form::factory()->fields([
        $this->namiField('members'),
        $this->textField('gender')->name('Geschlecht')->namiType(NamiType::GENDER)->required(true),
    ])
        ->create();

    $this->register($form, ['gender' => 'Herr', 'members' => [['id' => null, 'gender' => null]]])
        ->assertJsonValidationErrors(['members.0.gender' => 'Geschlecht für ein Mitglied ist erforderlich.']);
});

it('testItValidatesMembersFields', function () {
    $this->login()->loginNami();
    $this->createMember(['mitgliedsnr' => '5505']);
    $this->createMember(['mitgliedsnr' => '5506']);
    $form = Form::factory()->fields([
        $this->namiField('members'),
        $this->textField('other')->name('Andere')->required(true),
    ])
        ->create();

    $this->register($form, ['other' => 'ooo', 'members' => [['id' => '5505', 'other' => ''], ['id' => '5506', 'other' => '']]])
        ->assertJsonValidationErrors(['members.0.other' => 'Andere für ein Mitglied ist erforderlich.'])
        ->assertJsonValidationErrors(['members.1.other' => 'Andere für ein Mitglied ist erforderlich.']);
});

it('testItValidatesIfMemberExists', function () {
    $this->login()->loginNami();
    $form = Form::factory()->fields([
        $this->namiField('members'),
        $this->textField('other')->required(true),
    ])
        ->create();

    $this->register($form, ['other' => '::string::', 'members' => [['id' => '9999', 'other' => '::string::']]])
        ->assertJsonValidationErrors(['members.0.id' => 'Mitglied Nr 9999 ist nicht vorhanden.']);
});

it('testItValidatesMembersCheckboxesOptions', function () {
    $this->login()->loginNami();
    $this->createMember(['mitgliedsnr' => '5505']);
    $form = Form::factory()->fields([
        $this->namiField('members'),
        $this->checkboxesField('other')->name('Andere')->options(['A', 'B']),
    ])
        ->create();

    $this->register($form, ['other' => [], 'members' => [
        ['id' => '5505', 'other' => ['A', 'missing']]
    ]])
        ->assertJsonValidationErrors(['members.0.other.1' => 'Der gewählte Wert für Andere für ein Mitglied ist ungültig.']);
});

it('testItValidatesMembersCheckboxesAsArray', function () {
    $this->login()->loginNami();
    $this->createMember(['mitgliedsnr' => '5505']);
    $form = Form::factory()->fields([
        $this->namiField('members'),
        $this->checkboxesField('other')->name('Andere')->options(['A', 'B']),
    ])
        ->create();

    $this->register($form, ['other' => [], 'members' => [
        ['id' => '5505', 'other' => 'lala']
    ]])
        ->assertJsonValidationErrors(['members.0.other' => 'Andere für ein Mitglied muss ein Array sein.']);
});

it('testItSetsDefaultValueForFieldsThatAreNotNamiFillable', function () {
    $this->login()->loginNami();
    $this->createMember(['mitgliedsnr' => '5505', 'firstname' => 'Paula']);
    $form = Form::factory()->fields([
        $this->namiField('members'),
        $this->textField('other')->required(true)->forMembers(false)->options(['A', 'B']),
        $this->textField('firstname')->required(true)->namiType(NamiType::FIRSTNAME),
    ])
        ->create();

    $this->register($form, ['firstname' => 'A', 'other' => 'B', 'members' => [['id' => '5505']]])
        ->assertOk();
    $this->assertEquals('Paula', $form->participants->get(1)->data['firstname']);
    $this->assertEquals('', $form->participants->get(1)->data['other']);
});

it('testNamiFieldCanBeEmptyArray', function () {
    $this->login()->loginNami();
    $form = Form::factory()->fields([
        $this->namiField('members'),
    ])
        ->create();

    $this->register($form, ['members' => []])->assertOk();
    $this->assertDatabaseCount('participants', 1);
});

it('testNamiFieldMustBeArray', function () {
    $this->login()->loginNami();
    $form = Form::factory()->fields([
        $this->namiField('members'),
    ])
        ->create();

    $this->register($form, ['members' => null])->assertJsonValidationErrors(['members']);
});

it('testParticipantsHaveRelationToActualMember', function () {
    $this->login()->loginNami();
    $member = $this->createMember(['mitgliedsnr' => '5505']);
    $form = Form::factory()->fields([
        $this->namiField('members'),
    ])
        ->create();

    $this->register($form, ['members' => [['id' => '5505']]])->assertOk();
    $this->assertEquals($member->id, $form->participants->get(1)->member_id);
});

it('testItSetsRegionIdAndGroupIdOfParentGroup', function () {
    $this->login()->loginNami();
    $bezirk = Group::factory()->level(Level::REGION)->create();
    $stamm = Group::factory()->for($bezirk, 'parent')->level(Level::GROUP)->create();
    $this->createMember(['mitgliedsnr' => '5505', 'group_id' => $stamm->id]);
    $form = Form::factory()->fields([
        $this->namiField('members'),
        $this->groupField('bezirk')->forMembers(false)->namiType(NamiType::REGION),
        $this->groupField('stamm')->forMembers(false)->namiType(NamiType::STAMM),
    ])
        ->create();

    $this->register($form, ['bezirk' => $bezirk->id, 'stamm' => $stamm->id, 'members' => [['id' => '5505']]])->assertOk();
    $this->assertEquals($bezirk->id, $form->participants->get(1)->data['bezirk']);
    $this->assertEquals($stamm->id, $form->participants->get(1)->data['stamm']);
});

it('testItSetsRegionIfMemberIsDirectRegionMember', function () {
    $this->login()->loginNami();
    $bezirk = Group::factory()->level(Level::REGION)->create();
    $this->createMember(['mitgliedsnr' => '5505', 'group_id' => $bezirk->id]);
    $form = Form::factory()->fields([
        $this->namiField('members'),
        $this->groupField('bezirk')->forMembers(false)->namiType(NamiType::REGION),
    ])
        ->create();

    $this->register($form, ['bezirk' => $bezirk->id, 'members' => [['id' => '5505']]])->assertOk();
    $this->assertEquals($bezirk->id, $form->participants->get(1)->data['bezirk']);
});

it('registers via later link', function () {
    $this->login()->loginNami();
    $laterId = str()->uuid()->toString();
    $form = Form::factory()->fields([])
        ->registrationUntil(now()->subDay())
        ->create();
    Cache::set('later_'.$laterId, $form->id);

    $this->registerLater($form, [], $laterId)->assertOk();
    $this->assertDatabaseCount('participants', 1);
    $this->assertNull(Cache::get('later_'.$laterId));
});

it('checks signature of later link', function () {
    $this->login()->loginNami();
    $form = Form::factory()->fields([])
        ->registrationUntil(now()->subDay())
        ->create();

    $this->registerLaterWithWrongSignature($form, [], str()->uuid())->assertStatus(422);
    $this->assertDatabaseCount('participants', 0);
});

it('checks if later links is from current form', function () {
    $this->login()->loginNami();
    $foreignForm = Form::factory()->create();
    $form = Form::factory()->fields([])
        ->registrationUntil(now()->subDay())
        ->create();
    $laterId = str()->uuid()->toString();
    Cache::set('later_'.$laterId, $foreignForm->id);

    $this->registerLater($form, [], $laterId)->assertStatus(422);
    $this->assertDatabaseCount('participants', 0);
});
