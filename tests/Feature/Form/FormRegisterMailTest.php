<?php

namespace Tests\Feature\Form;

use App\Form\Enums\SpecialType;
use App\Form\Mails\ConfirmRegistrationMail;
use App\Form\Models\Form;
use App\Form\Models\Participant;
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
}
