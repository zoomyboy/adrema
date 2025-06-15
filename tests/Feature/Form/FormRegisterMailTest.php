<?php

namespace Tests\Feature\Form;

use App\Form\Enums\SpecialType;
use App\Form\Mails\ConfirmRegistrationMail;
use App\Form\Models\Form;
use App\Form\Models\Participant;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\RequestFactories\EditorRequestFactory;
use Tests\Lib\CreatesFormFields;

uses(DatabaseTransactions::class);
uses(CreatesFormFields::class);

beforeEach(function () {
    test()->setUpForm();
});

dataset('blocks', fn() => [
    fn () => [
        ['mode' => 'all', 'ifs' => []],
        test()->dropdownField('fieldkey')->options(['A', 'B']),
        ['fieldkey' => 'A'],
        true,
    ],

    fn () => [
        ['mode' => 'any', 'ifs' => []],
        test()->dropdownField('fieldkey')->options(['A', 'B']),
        ['fieldkey' => 'A'],
        true,
    ],

    fn () => [
        ['mode' => 'any', 'ifs' => []],
        test()->dropdownField('fieldkey')->options(['A', 'B']),
        ['fieldkey' => 'A'],
        true,
    ],

    fn () => [
        ['mode' => 'any', 'ifs' => [
            ['field' => 'fieldkey', 'comparator' => 'isEqual', 'value' => 'A']
        ]],
        test()->dropdownField('fieldkey')->options(['A', 'B']),
        ['fieldkey' => 'A'],
        true,
    ],

    fn () => [
        ['mode' => 'any', 'ifs' => [
            ['field' => 'fieldkey', 'comparator' => 'isEqual', 'value' => 'B']
        ]],
        test()->dropdownField('fieldkey')->options(['A', 'B']),
        ['fieldkey' => 'A'],
        false,
    ],

    fn () => [
        ['mode' => 'any', 'ifs' => [
            ['field' => 'fieldkey', 'comparator' => 'isEqual', 'value' => 'B'],
            ['field' => 'fieldkey', 'comparator' => 'isEqual', 'value' => 'A']
        ]],
        test()->dropdownField('fieldkey')->options(['A', 'B']),
        ['fieldkey' => 'A'],
        true,
    ],

    fn () => [
        ['mode' => 'any', 'ifs' => [
            ['field' => 'fieldkey', 'comparator' => 'isEqual', 'value' => 'B'],
            ['field' => 'fieldkey', 'comparator' => 'isEqual', 'value' => 'A']
        ]],
        test()->dropdownField('fieldkey')->options(['A', 'B']),
        ['fieldkey' => 'B'],
        true,
    ],

    fn () => [
        ['mode' => 'all', 'ifs' => [
            ['field' => 'fieldkey', 'comparator' => 'isEqual', 'value' => 'B'],
            ['field' => 'fieldkey', 'comparator' => 'isEqual', 'value' => 'A']
        ]],
        test()->dropdownField('fieldkey')->options(['A', 'B']),
        ['fieldkey' => 'B'],
        false,
    ],

    fn () => [
        ['mode' => 'all', 'ifs' => [
            ['field' => 'fieldkey', 'comparator' => 'isEqual', 'value' => 'B']
        ]],
        test()->dropdownField('fieldkey')->options(['A', 'B']),
        ['fieldkey' => 'B'],
        true,
    ],

    fn () => [
        ['mode' => 'all', 'ifs' => [
            ['field' => 'fieldkey', 'comparator' => 'isNotEqual', 'value' => 'A']
        ]],
        test()->dropdownField('fieldkey')->options(['A', 'B']),
        ['fieldkey' => 'B'],
        true,
    ],

    fn () => [
        ['mode' => 'all', 'ifs' => [
            ['field' => 'fieldkey', 'comparator' => 'isIn', 'value' => ['A']]
        ]],
        test()->dropdownField('fieldkey')->options(['A', 'B']),
        ['fieldkey' => 'A'],
        true,
    ],

    fn () => [
        ['mode' => 'all', 'ifs' => [
            ['field' => 'fieldkey', 'comparator' => 'isNotIn', 'value' => ['B']]
        ]],
        test()->dropdownField('fieldkey')->options(['A', 'B']),
        ['fieldkey' => 'A'],
        true,
    ],

    fn () => [
        ['mode' => 'all', 'ifs' => [
            ['field' => 'fieldkey', 'comparator' => 'isNotIn', 'value' => ['B']]
        ]],
        test()->radioField('fieldkey')->options(['A', 'B']),
        ['fieldkey' => 'A'],
        true,
    ],

    fn () => [
        ['mode' => 'all', 'ifs' => [
            ['field' => 'fieldkey', 'comparator' => 'isEqual', 'value' => true]
        ]],
        test()->checkboxField('fieldkey'),
        ['fieldkey' => true],
        true,
    ],

    fn () => [
        ['mode' => 'all', 'ifs' => [
            ['field' => 'fieldkey', 'comparator' => 'isEqual', 'value' => false]
        ]],
        test()->checkboxField('fieldkey'),
        ['fieldkey' => true],
        false,
    ],
]);


it('testItShowsFormContent', function () {
    $this->login()->loginNami()->withoutExceptionHandling();

    $participant = Participant::factory()->for(
        Form::factory()->sections([
            FormtemplateSectionRequest::new()->name('Persönliches')->fields([
                $this->textField('vorname')->name('Vorname')->specialType(SpecialType::FIRSTNAME),
                $this->textField('nachname')->specialType(SpecialType::LASTNAME),
            ])
        ])

            ->mailTop(EditorRequestFactory::new()->text(10, 'mail top'))->mailBottom(EditorRequestFactory::new()->text(11, 'mail bottom'))
    )
        ->data(['vorname' => 'Max', 'nachname' => 'Muster'])
        ->create();

    $mail = new ConfirmRegistrationMail($participant);
    $mail->assertSeeInText('mail top');
    $mail->assertSeeInText('mail bottom');
});

it('testItShowsParticipantGreeting', function () {
    $this->login()->loginNami()->withoutExceptionHandling();
    $participant = Participant::factory()->for(Form::factory()->sections([
        FormtemplateSectionRequest::new()->name('Persönliches')->fields([
            $this->textField('vorname')->name('Vorname')->specialType(SpecialType::FIRSTNAME),
            $this->textField('nachname')->specialType(SpecialType::LASTNAME),
            $this->checkboxField('fullyear')->name('Volljährig'),
        ])
    ]))
        ->data(['vorname' => 'Max', 'nachname' => 'Muster', 'fullyear' => true])
        ->create();

    $mail = new ConfirmRegistrationMail($participant);
    $mail->assertSeeInText('# Hallo Max Muster');
    $mail->assertSeeInText('## Persönliches');
    $mail->assertSeeInText('* Vorname: Max');
    $mail->assertSeeInText('* Volljährig: Ja');
});

it('testItAttachesMailAttachments', function () {
    $this->login()->loginNami()->withoutExceptionHandling();
    $participant = Participant::factory()->for(
        Form::factory()
            ->fields([
                $this->textField('vorname')->name('Vorname')->specialType(SpecialType::FIRSTNAME),
                $this->textField('nachname')->specialType(SpecialType::LASTNAME),
            ])
            ->withDocument('mailattachments', 'beispiel.pdf', 'content1')
            ->withDocument('mailattachments', 'beispiel2.pdf', 'content2')
    )
        ->data(['vorname' => 'Max', 'nachname' => 'Muster'])
        ->create();

    $mail = new ConfirmRegistrationMail($participant);
    $mail->assertHasAttachedData('content1', 'beispiel.pdf', ['mime' => 'application/pdf']);
    $mail->assertHasAttachedData('content2', 'beispiel2.pdf', ['mime' => 'application/pdf']);
});

/**
 * @param array<string, mixed> $conditions
 * @param array<string, mixed> $participantValues
 */
it('testItFiltersForBlockConditions', function (array $conditions, FormtemplateFieldRequest $field, array $participantValues, bool $result) {
    $this->login()->loginNami()->withoutExceptionHandling();

    $participant = Participant::factory()->for(
        Form::factory()
            ->fields([
                $field,
                $this->textField('firstname')->specialType(SpecialType::FIRSTNAME),
                $this->textField('lastname')->specialType(SpecialType::LASTNAME),
            ])
            ->mailTop(EditorRequestFactory::new()->text(10, '::content::', $conditions))
    )
        ->data(['firstname' => 'Max', 'lastname' => 'Muster', ...$participantValues])
        ->create();

    $mail = new ConfirmRegistrationMail($participant);
    if ($result) {
        $mail->assertSeeInText('::content::');
    } else {
        $mail->assertDontSeeInText('::content::');
    }
})->with('blocks');

/**
 * @param array<string, mixed> $conditions
 * @param array<string, mixed> $participantValues
 */
it('testItFiltersForAttachments', function (array $conditions, FormtemplateFieldRequest $field, array $participantValues, bool $result) {
    $this->login()->loginNami()->withoutExceptionHandling();

    $participant = Participant::factory()->for(
        Form::factory()
            ->fields([
                $field,
                $this->textField('firstname')->specialType(SpecialType::FIRSTNAME),
                $this->textField('lastname')->specialType(SpecialType::LASTNAME),
            ])
            ->withDocument('mailattachments', 'beispiel.pdf', 'content', ['conditions' => $conditions])
    )
        ->data(['firstname' => 'Max', 'lastname' => 'Muster', ...$participantValues])
        ->create();

    $mail = new ConfirmRegistrationMail($participant);
    $mail->assertSeeInHtml('Daten');
    if ($result) {
        $this->assertTrue($mail->hasAttachedData('content', 'beispiel.pdf', ['mime' => 'application/pdf']));
    } else {
        $this->assertFalse($mail->hasAttachedData('content', 'beispiel.pdf', ['mime' => 'application/pdf']));
    }
})->with('blocks');
