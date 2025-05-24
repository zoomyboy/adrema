<?php

namespace Tests\Feature\Member;

use App\Prevention\Enums\Prevention;
use App\Form\Actions\PreventionRememberAction;
use App\Form\Enums\NamiType;
use App\Form\Enums\SpecialType;
use App\Form\Models\Form;
use App\Form\Models\Participant;
use App\Invoice\InvoiceSettings;
use App\Lib\Editor\Condition;
use App\Prevention\Mails\PreventionRememberMail;
use App\Member\Member;
use App\Member\Membership;
use App\Prevention\PreventionSettings;
use Generator;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Lib\CreatesFormFields;
use Tests\RequestFactories\EditorRequestFactory;
use Tests\TestCase;

uses(DatabaseTransactions::class);
uses(CreatesFormFields::class);

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

dataset('attributes', fn() => [
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

it('testItRemembersNonLeaders', function () {
    Mail::fake();
    $form = createForm();
    $participant = createParticipant($form);
    $participant->member->memberships->each->delete();

    PreventionRememberAction::run();

    $this->assertNotNull($participant->fresh()->last_remembered_at);
});


it('testItRemembersMember', function ($attrs, $preventions) {
    Mail::fake();
    $form = createForm();
    $participant = createParticipant($form);
    $participant->member->update($attrs);

    PreventionRememberAction::run();

    if (count($preventions)) {
        Mail::assertSent(PreventionRememberMail::class, fn($mail) => $mail->preventable->preventions() === $preventions);
        $this->assertNotNull($participant->fresh()->last_remembered_at);
    } else {
        Mail::assertNotSent(PreventionRememberMail::class);
        $this->assertNull($participant->fresh()->last_remembered_at);
    }
})->with('attributes');

it('testItDoesntRememberParticipantThatHasNoMail', function () {
    Mail::fake();
    $form = createForm();
    $participant = createParticipant($form);
    $participant->update(['data' => [...$participant->data, 'email' => '']]);

    PreventionRememberAction::run();

    Mail::assertNotSent(PreventionRememberMail::class);
});

it('testItRendersMail', function () {
    InvoiceSettings::fake(['from_long' => 'Stamm Beispiel']);
    $form = createForm();
    $participant = createParticipant($form);
    (new PreventionRememberMail($participant, app(PreventionSettings::class)->formmail))
        ->assertSeeInText($participant->member->firstname)
        ->assertSeeInText($participant->member->lastname)
        ->assertSeeInText('Stamm Beispiel');
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
    ]));
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

it('testItDisplaysBodyTextInMail', function () {
    $form = createForm();
    $participant = createParticipant($form);

    $mail = new PreventionRememberMail($participant, EditorRequestFactory::new()->paragraphs(['ggtt'])->toData());
    $mail->assertSeeInText('ggtt');
});
