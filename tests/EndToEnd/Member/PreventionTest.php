<?php

namespace Tests\EndToEnd\Member;

use App\Prevention\Enums\Prevention;
use App\Form\Actions\PreventionRememberAction;
use App\Form\Enums\NamiType;
use App\Form\Enums\SpecialType;
use App\Form\Models\Form;
use App\Form\Models\Participant;
use App\Invoice\InvoiceSettings;
use App\Lib\Editor\Condition;
use App\Member\FilterScope;
use App\Prevention\Mails\PreventionRememberMail;
use App\Member\Member;
use App\Member\Membership;
use App\Prevention\Actions\YearlyRememberAction;
use App\Prevention\Mails\YearlyMail;
use App\Prevention\PreventionSettings;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Mail;
use Tests\EndToEndTestCase;
use Tests\Lib\CreatesFormFields;
use Tests\RequestFactories\EditorRequestFactory;

uses(DatabaseTransactions::class);
uses(CreatesFormFields::class);
uses(EndToEndTestCase::class);

beforeEach(function () {
    app(PreventionSettings::class)->fill(['preventAgainst' => array_column(Prevention::values(), 'id'), 'active' => true])->save();
});

function createForm(): Form
{
    return Form::factory()->fields([
        test()->textField('vorname')->namiType(NamiType::FIRSTNAME)->specialType(SpecialType::FIRSTNAME),
        test()->textField('nachname')->namiType(NamiType::FIRSTNAME)->specialType(SpecialType::LASTNAME),
        test()->textField('email')->namiType(NamiType::FIRSTNAME)->specialType(SpecialType::EMAIL),
    ])->create(['needs_prevention' => true]);
}

function createParticipant(Form $form): Participant
{
    return Participant::factory()->for($form)->data([
        'vorname' => 'Max',
        'nachname' => 'Muster',
        'email' => 'mail@a.de',
    ])->for(Member::factory()->defaults()->has(Membership::factory()->inLocal('€ LeiterIn', 'Wölfling')))->create();
}

function createMember(array $attributes): Member
{
    return Member::factory()->defaults()->has(Membership::factory()->inLocal('€ LeiterIn', 'Wölfling'))->create($attributes);
}

dataset('attributes', fn() => [
    [
        ['has_vk' => false, 'efz' => null, 'ps_at' => null],
        [Prevention::EFZ, Prevention::VK, Prevention::PS]
    ],

    [
        ['has_vk' => true, 'efz' => null, 'ps_at' => now()],
        [Prevention::EFZ]
    ],

    [
        ['has_vk' => true, 'efz' => now(), 'ps_at' => null],
        [Prevention::PS]
    ],

    [
        ['has_vk' => true, 'efz' => now()->subDay(), 'ps_at' => now()],
        []
    ],

    [
        ['has_vk' => true, 'efz' => now(), 'ps_at' => now()->subDay()],
        []
    ],

    [
        ['has_vk' => true, 'efz' => now()->subYears(5)->subDay(), 'ps_at' => now()],
        [Prevention::EFZ]
    ],

    [
        ['has_vk' => true, 'efz' => now(), 'ps_at' => null],
        [Prevention::PS]
    ],

    [
        ['has_vk' => true, 'efz' => now(), 'ps_at' => now()->subYears(5)->subDay()],
        [Prevention::MOREPS]
    ],

    [
        ['has_vk' => true, 'efz' => now(), 'ps_at' => now()->subYears(5)->subDay(), 'more_ps_at' => now()],
        []
    ],

    [
        ['has_vk' => true, 'efz' => now(), 'ps_at' => now()->subYears(15), 'more_ps_at' => now()->subYears(5)->subDay()],
        [Prevention::MOREPS],
    ],

    [
        ['has_vk' => false, 'efz' => now(), 'ps_at' => now()],
        [Prevention::VK],
    ],

    [
        ['has_vk' => true, 'efz' => now(), 'ps_at' => now()->subYears(7)],
        [Prevention::MOREPS],
    ]
]);

it('testItRemembersWhenNotRememberedYet', function () {
    Mail::fake();
    $form = createForm();
    $participant = createParticipant($form);

    PreventionRememberAction::run();

    $this->assertEquals(now()->format('Y-m-d'), $participant->fresh()->last_remembered_at->format('Y-m-d'));
});

it('testItDoesntRememberPastEvents', function () {
    Mail::fake();
    $form = createForm();
    $participant = createParticipant($form);
    $form->update(['from' => now()->subDay()]);

    PreventionRememberAction::run();

    $this->assertNull($participant->fresh()->last_remembered_at);
});

it('testItDoesntRememberWhenConditionDoesntMatch', function () {
    Mail::fake();
    $form = createForm();
    $form->update(['prevention_conditions' => Condition::from(['mode' => 'all', 'ifs' => [['field' => 'vorname', 'comparator' => 'isEqual', 'value' => 'Max']]])]);
    $participant = createParticipant($form);
    $participant->update(['data' => [...$participant->data, 'vorname' => 'Jane']]);

    PreventionRememberAction::run();

    $this->assertNull($participant->fresh()->last_remembered_at);
});

it('testItRemembersWhenRememberIsDue', function () {
    Mail::fake();
    $form = createForm();
    $participant = tap(createParticipant($form), fn($p) => $p->update(['last_remembered_at' => now()->subWeeks(3)]));

    PreventionRememberAction::run();

    $this->assertEquals(now()->format('Y-m-d'), $participant->fresh()->last_remembered_at->format('Y-m-d'));
});

it('testItDoesntRememberWhenRememberingIsNotDue', function () {
    Mail::fake();
    $form = createForm();
    $participant = tap(createParticipant($form), fn($p) => $p->update(['last_remembered_at' => now()->subWeeks(1)]));

    PreventionRememberAction::run();

    $this->assertEquals(now()->subWeeks(1)->format('Y-m-d'), $participant->fresh()->last_remembered_at->format('Y-m-d'));
});

it('testItDoesntRememberWhenFormDoesntNeedPrevention', function () {
    Mail::fake();
    $form = tap(createForm(), fn($form) => $form->update(['needs_prevention' => false]));
    $participant = createParticipant($form);

    PreventionRememberAction::run();

    $this->assertNull($participant->fresh()->last_remembered_at);
});

it('testItDoesntRememberWhenParticipantDoesntHaveMember', function () {
    Mail::fake();
    $form = createForm();
    $participant = createParticipant($form);
    $participant->member->delete();

    PreventionRememberAction::run();

    $this->assertNull($participant->fresh()->last_remembered_at);
});

it('doesnt remember non leaders', function () {
    Mail::fake();
    $form = createForm();
    $participant = createParticipant($form);
    $participant->member->memberships->each->delete();

    PreventionRememberAction::run();

    $this->assertNotNull($participant->fresh()->last_remembered_at);
});


it('remembers event participant', function ($attrs, $preventions) {
    Mail::fake();
    $form = createForm();
    $participant = createParticipant($form);
    $participant->member->update($attrs);

    PreventionRememberAction::run();

    if (count($preventions)) {
        Mail::assertSent(PreventionRememberMail::class, fn($mail) => $mail->preventable->preventions()->pluck('type')->toArray() === $preventions);
        $this->assertNotNull($participant->fresh()->last_remembered_at);
    } else {
        Mail::assertNotSent(PreventionRememberMail::class);
        $this->assertNull($participant->fresh()->last_remembered_at);
    }
})->with('attributes');

it('sets due date in mail when not now', function () {
    Mail::fake();
    $form = createForm();
    $form->update(['from' => now()->addMonths(8)]);
    $participant = createParticipant($form);
    $participant->member->update(['efz' =>  now()->subYears(5)->addMonth(), 'ps_at' => now(), 'has_vk' => true]);

    PreventionRememberAction::run();

    Mail::assertSent(PreventionRememberMail::class, fn($mail) => $mail->preventable->preventions()->first()->expires->isSameDay(now()->addMonth()));
});

it('notices a few weeks before', function ($date, bool $shouldSend) {
    Mail::fake();
    app(PreventionSettings::class)->fill(['weeks' => 2])->save();
    createMember(['efz' => $date, 'ps_at' => now(), 'has_vk' => true]);

    sleep(2);
    YearlyRememberAction::run();

    $shouldSend
        ? Mail::assertSent(YearlyMail::class, fn($mail) => $mail->preventions->first()->expires->isSameDay(now()->addWeeks(2)))
        : Mail::assertNotSent(YearlyMail::class);
})->with([
    [fn() => now()->subYears(5)->addWeeks(2), true],
    [fn() => now()->subYears(5)->addWeeks(2)->addDay(), false],
    [fn() => now()->subYears(5)->addWeeks(2)->subDay(), false],
]);

it('remembers members yearly', function ($date, $shouldSend) {
    Mail::fake();
    createMember(['efz' => $date, 'ps_at' => now(), 'has_vk' => true]);

    sleep(2);
    YearlyRememberAction::run();

    $shouldSend
        ? Mail::assertSent(YearlyMail::class, fn($mail) => $mail->preventions->first()->expires->isSameDay(now()))
        : Mail::assertNotSent(YearlyMail::class);
})->with([
    [fn() => now()->subYears(5), true],
    [fn() => now()->subYears(5)->addDay(), false],
    [fn() => now()->subYears(5)->subDay(), false],
]);

it('remembers yearly only once', function () {
    Mail::fake();
    createMember(['efz' => now()->subYears(5), 'ps_at' => now(), 'has_vk' => true]);

    sleep(2);
    YearlyRememberAction::run();
    YearlyRememberAction::run();
    YearlyRememberAction::run();

    Mail::assertSentCount(1);
    Mail::assertSent(YearlyMail::class, fn($mail) => $mail->preventions->first()->expires->isSameDay(now()));
});

it('testItDoesntRememberParticipantThatHasNoMail', function () {
    Mail::fake();
    $form = createForm();
    $participant = createParticipant($form);
    $participant->update(['data' => [...$participant->data, 'email' => '']]);

    PreventionRememberAction::run();

    Mail::assertNotSent(PreventionRememberMail::class);
});

it('doesnt remember when prevent against doesnt match', function () {
    Mail::fake();
    app(PreventionSettings::class)->fill(['preventAgainst' => []])->save();
    createMember(['efz' => now()->subYears(5), 'ps_at' => now(), 'has_vk' => true]);

    sleep(2);
    YearlyRememberAction::run();

    Mail::assertNotSent(YearlyMail::class);
});

it('doesnt send yearly mail when member has no mail', function () {
    Mail::fake();
    createMember(['efz' => now()->subYears(5), 'ps_at' => now(), 'has_vk' => true, 'email' => '', 'email_parents' => '']);

    sleep(2);
    YearlyRememberAction::run();

    Mail::assertNotSent(YearlyMail::class);
});

it('doesnt send yearly mail when yearly sending is deactivated', function () {
    Mail::fake();
    app(PreventionSettings::class)->fill(['active' => false])->save();
    createMember(['efz' => now()->subYears(5), 'ps_at' => now(), 'has_vk' => true]);

    sleep(2);
    YearlyRememberAction::run();

    Mail::assertNotSent(YearlyMail::class);
});

it('doesnt send yearly mail when member doesnt match', function () {
    Mail::fake();
    app(PreventionSettings::class)->fill([
        'yearlyMemberFilter' => FilterScope::from(['search' => 'Lorem Ipsum']),
    ])->save();
    createMember(['efz' => now()->subYears(5), 'ps_at' => now(), 'has_vk' => true, 'firstname' => 'Max', 'lastname' => 'Muster']);

    sleep(2);
    YearlyRememberAction::run();

    Mail::assertNotSent(YearlyMail::class);
});

it('testItRendersSetttingMail', function () {
    Mail::fake();
    app(PreventionSettings::class)->fill([
        'formmail' => EditorRequestFactory::new()->paragraphs(["lorem lala {formname} g", "{wanted}", "bbb"])->toData()
    ])->save();
    $form = createForm();
    createParticipant($form);

    PreventionRememberAction::run();

    Mail::assertSent(PreventionRememberMail::class, fn($mail) => $mail->bodyText->hasAll([
        'lorem lala ' . $form->name,
        'erweitertes'
    ]) && $mail->bodyText->hasNot(now()->format('d.m.Y')));
});

it('testItAppendsTextOfForm', function () {
    Mail::fake();
    app(PreventionSettings::class)->fill([
        'formmail' => EditorRequestFactory::new()->paragraphs(["::first::"])->toData()
    ])->save();
    $form = createForm();
    $form->update(['prevention_text' => EditorRequestFactory::new()->paragraphs(['event'])->toData()]);
    createParticipant($form);

    PreventionRememberAction::run();

    Mail::assertSent(PreventionRememberMail::class, fn($mail) => $mail->bodyText->hasAll([
        'event'
    ]));
});

it('testItDoesntAppendTextTwice', function () {
    Mail::fake();
    app(PreventionSettings::class)->fill(['frommail' => EditorRequestFactory::new()->paragraphs(["::first::"])->toData()])->save();
    tap(createForm(), function ($f) {
        $f->update(['prevention_text' => EditorRequestFactory::new()->paragraphs(['oberhausen'])->toData()]);
        createParticipant($f);
    });
    tap(createForm(), function ($f) {
        $f->update(['prevention_text' => EditorRequestFactory::new()->paragraphs(['siegburg'])->toData()]);
        createParticipant($f);
    });

    PreventionRememberAction::run();

    Mail::assertSent(PreventionRememberMail::class, fn($mail) => $mail->bodyText->hasAll(['oberhausen']) && !$mail->bodyText->hasAll(['siegburg']));
});

/* ----------------------------------------- Mail contents ----------------------------------------- */
it('displays body text in prevention remember mail', function () {
    $form = createForm();
    $participant = createParticipant($form);

    $mail = new PreventionRememberMail($participant, EditorRequestFactory::new()->paragraphs(['ggtt'])->toData(), collect([]));
    $mail->assertSeeInText('ggtt');
});

it('renders prevention mail for events with group name', function () {
    InvoiceSettings::fake(['from_long' => 'Stamm Beispiel']);
    $form = createForm();
    $participant = createParticipant($form);
    (new PreventionRememberMail($participant, app(PreventionSettings::class)->formmail, collect([])))
        ->assertSeeInText('Max')
        ->assertSeeInText('Muster')
        ->assertSeeInText('Stamm Beispiel');
});

it('renders yearly mail', function () {
    InvoiceSettings::fake(['from_long' => 'Stamm Beispiel']);
    $member = createMember([]);
    $mail = new YearlyMail($member, EditorRequestFactory::new()->paragraphs(['ggtt'])->toData(), collect([]));
    $mail
        ->assertSeeInText('ggtt')
        ->assertSeeInText('Stamm Beispiel');
});

it('renders setting of yearly mail', function () {
    Mail::fake();
    app(PreventionSettings::class)->fill([
        'yearlymail' => EditorRequestFactory::new()->paragraphs(["{wanted}", "bbb"])->toData()
    ])->save();
    createMember((['efz' =>  now()->subYears(5), 'ps_at' => now(), 'has_vk' => true]));

    sleep(2);
    YearlyRememberAction::run();

    Mail::assertSent(
        YearlyMail::class,
        fn($mail) => $mail->bodyText->hasAll(['erweitertes', 'bbb'])
            && $mail->bodyText->hasNot(now()->format('d.m.Y'))
    );
});

it('renders expires at date for preventions', function () {
    Mail::fake();
    app(PreventionSettings::class)->fill([
        'yearlymail' => EditorRequestFactory::new()->paragraphs(["{wanted}"])->toData(),
        'weeks' => 4,
    ])->save();
    createMember((['efz' =>  now()->subYears(5)->addWeeks(4), 'ps_at' => now(), 'has_vk' => true]));

    sleep(2);
    YearlyRememberAction::run();

    Mail::assertSent(YearlyMail::class, fn($mail) => $mail->bodyText->hasAll([
        'erweitertes',
        'am ' . now()->addWeeks(4)->format('d.m.Y'),
    ]) && $mail->bodyText->hasNot(now()->format('d.m.Y')));
});
