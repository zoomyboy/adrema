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
use Tests\Lib\CreatesFormFields;
use Tests\RequestFactories\EditorRequestFactory;
use Tests\TestCase;

class PreventionTest extends TestCase
{

    use DatabaseTransactions;
    use CreatesFormFields;

    public function testItRemembersWhenNotRememberedYet(): void
    {
        Mail::fake();
        $form = $this->createForm();
        $participant = $this->createParticipant($form);

        PreventionRememberAction::run();

        $this->assertEquals(now()->format('Y-m-d'), $participant->fresh()->last_remembered_at->format('Y-m-d'));
    }

    public function testItDoesntRememberWhenConditionDoesntMatch(): void
    {
        Mail::fake();
        $form = $this->createForm();
        $form->update(['prevention_conditions' => Condition::from(['mode' => 'all', 'ifs' => [['field' => 'vorname', 'comparator' => 'isEqual', 'value' => 'Max']]])]);
        $participant = $this->createParticipant($form);
        $participant->update(['data' => [...$participant->data, 'vorname' => 'Jane']]);

        PreventionRememberAction::run();

        $this->assertNull($participant->fresh()->last_remembered_at);
    }

    public function testItRemembersWhenRememberIsDue(): void
    {
        Mail::fake();
        $form = $this->createForm();
        $participant = tap($this->createParticipant($form), fn ($p) => $p->update(['last_remembered_at' => now()->subWeeks(3)]));

        PreventionRememberAction::run();

        $this->assertEquals(now()->format('Y-m-d'), $participant->fresh()->last_remembered_at->format('Y-m-d'));
    }

    public function testItDoesntRememberWhenRememberingIsNotDue(): void
    {
        Mail::fake();
        $form = $this->createForm();
        $participant = tap($this->createParticipant($form), fn ($p) => $p->update(['last_remembered_at' => now()->subWeeks(1)]));

        PreventionRememberAction::run();

        $this->assertEquals(now()->subWeeks(1)->format('Y-m-d'), $participant->fresh()->last_remembered_at->format('Y-m-d'));
    }

    public function testItDoesntRememberWhenFormDoesntNeedPrevention(): void
    {
        Mail::fake();
        $form = tap($this->createForm(), fn ($form) => $form->update(['needs_prevention' => false]));
        $participant = $this->createParticipant($form);

        PreventionRememberAction::run();

        $this->assertNull($participant->fresh()->last_remembered_at);
    }

    public function testItDoesntRememberWhenParticipantDoesntHaveMember(): void
    {
        Mail::fake();
        $form = $this->createForm();
        $participant = $this->createParticipant($form);
        $participant->member->delete();

        PreventionRememberAction::run();

        $this->assertNull($participant->fresh()->last_remembered_at);
    }

    public function testItRemembersNonLeaders(): void
    {
        Mail::fake();
        $form = $this->createForm();
        $participant = $this->createParticipant($form);
        $participant->member->memberships->each->delete();

        PreventionRememberAction::run();

        $this->assertNotNull($participant->fresh()->last_remembered_at);
    }

    protected function attributes(): Generator
    {
        yield [
            'attrs' => ['has_vk' => true, 'efz' => null, 'ps_at' => now()],
            'preventions' => [Prevention::EFZ]
        ];

        yield [
            'attrs' => ['has_vk' => true, 'efz' => now(), 'ps_at' => null],
            'preventions' => [Prevention::PS]
        ];

        yield [
            'attrs' => ['has_vk' => true, 'efz' => now()->subDay(), 'ps_at' => now()],
            'preventions' => []
        ];

        yield [
            'attrs' => ['has_vk' => true, 'efz' => now(), 'ps_at' => now()->subDay()],
            'preventions' => []
        ];

        yield [
            'attrs' => ['has_vk' => true, 'efz' => now()->subYears(5)->subDay(), 'ps_at' => now()],
            'preventions' => [Prevention::EFZ]
        ];

        yield [
            'attrs' => ['has_vk' => true, 'efz' => now(), 'ps_at' => now()->subYears(5)->subDay()],
            'preventions' => [Prevention::PS]
        ];

        yield [
            'attrs' => ['has_vk' => true, 'efz' => now(), 'ps_at' => now()->subYears(5)->subDay(), 'more_ps_at' => now()],
            'preventions' => []
        ];

        yield [
            'attrs' => ['has_vk' => true, 'efz' => now(), 'ps_at' => now()->subYears(15), 'more_ps_at' => now()->subYears(5)->subDay()],
            'preventions' => [Prevention::MOREPS],
        ];

        yield [
            'attrs' => ['has_vk' => false, 'efz' => now(), 'ps_at' => now()],
            'preventions' => [Prevention::VK],
        ];
    }

    /**
     * @param array<int, Prevention> $preventions
     * @param array<string, mixed> $memberAttributes
     * @dataProvider attributes
     */
    public function testItRemembersMember(array $memberAttributes, array $preventions): void
    {
        Mail::fake();
        $form = $this->createForm();
        $participant = $this->createParticipant($form);
        $participant->member->update($memberAttributes);

        PreventionRememberAction::run();

        if (count($preventions)) {
            Mail::assertSent(PreventionRememberMail::class, fn ($mail) => $mail->preventable->preventions() === $preventions);
            $this->assertNotNull($participant->fresh()->last_remembered_at);
        } else {
            Mail::assertNotSent(PreventionRememberMail::class);
            $this->assertNull($participant->fresh()->last_remembered_at);
        }
    }

    public function testItDoesntRememberParticipantThatHasNoMail(): void
    {
        Mail::fake();
        $form = $this->createForm();
        $participant = $this->createParticipant($form);
        $participant->update(['data' => [...$participant->data, 'email' => '']]);

        PreventionRememberAction::run();

        Mail::assertNotSent(PreventionRememberMail::class);
    }

    public function testItRendersMail(): void
    {
        InvoiceSettings::fake(['from_long' => 'Stamm Beispiel']);
        $form = $this->createForm();
        $participant = $this->createParticipant($form);
        (new PreventionRememberMail($participant, app(PreventionSettings::class)->formmail))
            ->assertSeeInText($participant->member->firstname)
            ->assertSeeInText($participant->member->lastname)
            ->assertSeeInText('Stamm Beispiel');
    }

    public function testItRendersSetttingMail(): void
    {
        Mail::fake();
        app(PreventionSettings::class)->fill([
            'formmail' => EditorRequestFactory::new()->paragraphs(["lorem lala {formname} g", "{wanted}", "bbb"])->toData()
        ])->save();
        $form = $this->createForm();
        $this->createParticipant($form);

        PreventionRememberAction::run();

        Mail::assertSent(PreventionRememberMail::class, fn ($mail) => $mail->bodyText->hasAll([
            'lorem lala ' . $form->name,
            'erweitertes'
        ]));
    }

    public function testItAppendsTextOfForm(): void
    {
        Mail::fake();
        app(PreventionSettings::class)->fill([
            'formmail' => EditorRequestFactory::new()->paragraphs(["::first::"])->toData()
        ])->save();
        $form = $this->createForm();
        $form->update(['prevention_text' => EditorRequestFactory::new()->paragraphs(['event'])->toData()]);
        $this->createParticipant($form);

        PreventionRememberAction::run();

        Mail::assertSent(PreventionRememberMail::class, fn ($mail) => $mail->bodyText->hasAll([
            'event'
        ]));
    }

    public function testItDoesntAppendTextTwice(): void
    {
        Mail::fake();
        app(PreventionSettings::class)->fill(['frommail' => EditorRequestFactory::new()->paragraphs(["::first::"])->toData()])->save();
        tap($this->createForm(), function ($f) {
            $f->update(['prevention_text' => EditorRequestFactory::new()->paragraphs(['oberhausen'])->toData()]);
            $this->createParticipant($f);
        });
        tap($this->createForm(), function ($f) {
            $f->update(['prevention_text' => EditorRequestFactory::new()->paragraphs(['siegburg'])->toData()]);
            $this->createParticipant($f);
        });

        PreventionRememberAction::run();

        Mail::assertSent(PreventionRememberMail::class, fn ($mail) => $mail->bodyText->hasAll(['oberhausen']) && !$mail->bodyText->hasAll(['siegburg']));
    }

    public function testItDisplaysBodyTextInMail(): void
    {
        $form = $this->createForm();
        $participant = $this->createParticipant($form);

        $mail = new PreventionRememberMail($participant, EditorRequestFactory::new()->paragraphs(['ggtt'])->toData());
        $mail->assertSeeInText('ggtt');
    }

    protected function createForm(): Form
    {
        return Form::factory()->fields([
            $this->textField('vorname')->namiType(NamiType::FIRSTNAME)->specialType(SpecialType::FIRSTNAME),
            $this->textField('nachname')->namiType(NamiType::FIRSTNAME)->specialType(SpecialType::LASTNAME),
            $this->textField('email')->namiType(NamiType::FIRSTNAME)->specialType(SpecialType::EMAIL),
        ])->create(['needs_prevention' => true]);
    }

    protected function createParticipant(Form $form): Participant
    {
        return Participant::factory()->for($form)->data([
            'vorname' => 'Max',
            'nachname' => 'Muster',
            'email' => 'mail@a.de',
        ])->for(Member::factory()->defaults()->has(Membership::factory()->inLocal('€ LeiterIn', 'Wölfling')))->create();
    }
}
