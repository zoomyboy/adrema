<?php

namespace Tests\Feature\Form;

use App\Form\Enums\SpecialType;
use App\Form\Mails\ConfirmRegistrationMail;
use App\Form\Models\Form;
use App\Form\Models\Participant;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FormRegisterMailTest extends FormTestCase
{

    use DatabaseTransactions;

    public function testItShowsFormContent(): void
    {
        $this->login()->loginNami()->withoutExceptionHandling();
        $participant = Participant::factory()
            ->for(Form::factory()->mailTop('mail top')->mailBottom('mail bottom'))->create();

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
            ])
        ]))
            ->data(['vorname' => 'Max', 'nachname' => 'Muster'])
            ->create();

        $mail = new ConfirmRegistrationMail($participant);
        $mail->assertSeeInText('# Hallo Max Muster');
        $mail->assertSeeInText('## Persönliches');
        $mail->assertSeeInText('* Vorname: Max');
    }
}
