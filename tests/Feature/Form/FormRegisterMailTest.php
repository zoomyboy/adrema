<?php

namespace Tests\Feature\Form;

use App\Form\Enums\SpecialType;
use App\Form\Mails\ConfirmRegistrationMail;
use App\Form\Models\Form;
use App\Form\Models\Participant;
use Generator;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\RequestFactories\EditorRequestFactory;

class FormRegisterMailTest extends FormTestCase
{

    use DatabaseTransactions;

    public function testItShowsFormContent(): void
    {
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
    }

    public function testItShowsParticipantGreeting(): void
    {
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
    }

    public function testItAttachesMailAttachments(): void
    {
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
    }

    public function blockDataProvider(): Generator
    {
        yield [
            ['mode' => 'all', 'ifs' => []],
            $this->dropdownField('fieldkey')->options(['A', 'B']),
            ['fieldkey' => 'A'],
            true,
        ];

        yield [
            ['mode' => 'any', 'ifs' => []],
            $this->dropdownField('fieldkey')->options(['A', 'B']),
            ['fieldkey' => 'A'],
            true,
        ];

        yield [
            ['mode' => 'any', 'ifs' => []],
            $this->dropdownField('fieldkey')->options(['A', 'B']),
            ['fieldkey' => 'A'],
            true,
        ];

        yield [
            ['mode' => 'any', 'ifs' => [
                ['field' => 'fieldkey', 'comparator' => 'isEqual', 'value' => 'A']
            ]],
            $this->dropdownField('fieldkey')->options(['A', 'B']),
            ['fieldkey' => 'A'],
            true,
        ];

        yield [
            ['mode' => 'any', 'ifs' => [
                ['field' => 'fieldkey', 'comparator' => 'isEqual', 'value' => 'B']
            ]],
            $this->dropdownField('fieldkey')->options(['A', 'B']),
            ['fieldkey' => 'A'],
            false,
        ];

        yield [
            ['mode' => 'any', 'ifs' => [
                ['field' => 'fieldkey', 'comparator' => 'isEqual', 'value' => 'B'],
                ['field' => 'fieldkey', 'comparator' => 'isEqual', 'value' => 'A']
            ]],
            $this->dropdownField('fieldkey')->options(['A', 'B']),
            ['fieldkey' => 'A'],
            true,
        ];

        yield [
            ['mode' => 'any', 'ifs' => [
                ['field' => 'fieldkey', 'comparator' => 'isEqual', 'value' => 'B'],
                ['field' => 'fieldkey', 'comparator' => 'isEqual', 'value' => 'A']
            ]],
            $this->dropdownField('fieldkey')->options(['A', 'B']),
            ['fieldkey' => 'B'],
            true,
        ];

        yield [
            ['mode' => 'all', 'ifs' => [
                ['field' => 'fieldkey', 'comparator' => 'isEqual', 'value' => 'B'],
                ['field' => 'fieldkey', 'comparator' => 'isEqual', 'value' => 'A']
            ]],
            $this->dropdownField('fieldkey')->options(['A', 'B']),
            ['fieldkey' => 'B'],
            false,
        ];

        yield [
            ['mode' => 'all', 'ifs' => [
                ['field' => 'fieldkey', 'comparator' => 'isEqual', 'value' => 'B']
            ]],
            $this->dropdownField('fieldkey')->options(['A', 'B']),
            ['fieldkey' => 'B'],
            true,
        ];

        yield [
            ['mode' => 'all', 'ifs' => [
                ['field' => 'fieldkey', 'comparator' => 'isNotEqual', 'value' => 'A']
            ]],
            $this->dropdownField('fieldkey')->options(['A', 'B']),
            ['fieldkey' => 'B'],
            true,
        ];

        yield [
            ['mode' => 'all', 'ifs' => [
                ['field' => 'fieldkey', 'comparator' => 'isIn', 'value' => ['A']]
            ]],
            $this->dropdownField('fieldkey')->options(['A', 'B']),
            ['fieldkey' => 'A'],
            true,
        ];

        yield [
            ['mode' => 'all', 'ifs' => [
                ['field' => 'fieldkey', 'comparator' => 'isNotIn', 'value' => ['B']]
            ]],
            $this->dropdownField('fieldkey')->options(['A', 'B']),
            ['fieldkey' => 'A'],
            true,
        ];

        yield [
            ['mode' => 'all', 'ifs' => [
                ['field' => 'fieldkey', 'comparator' => 'isNotIn', 'value' => ['B']]
            ]],
            $this->radioField('fieldkey')->options(['A', 'B']),
            ['fieldkey' => 'A'],
            true,
        ];

        yield [
            ['mode' => 'all', 'ifs' => [
                ['field' => 'fieldkey', 'comparator' => 'isEqual', 'value' => true]
            ]],
            $this->checkboxField('fieldkey'),
            ['fieldkey' => true],
            true,
        ];

        yield [
            ['mode' => 'all', 'ifs' => [
                ['field' => 'fieldkey', 'comparator' => 'isEqual', 'value' => false]
            ]],
            $this->checkboxField('fieldkey'),
            ['fieldkey' => true],
            false,
        ];
    }

    /**
     * @dataProvider blockDataProvider
     */
    public function testItFiltersForBlockConditions(array $conditions, FormtemplateFieldRequest $field, array $participantValues, bool $result): void
    {
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
    }
}
