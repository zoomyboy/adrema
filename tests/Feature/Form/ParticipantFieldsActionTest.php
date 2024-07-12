<?php

namespace Tests\Feature\Form;

use App\Form\Models\Form;
use App\Form\Models\Participant;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ParticipantFieldsActionTest extends FormTestCase
{

    use DatabaseTransactions;

    public function testItShowsParticipantsFields(): void
    {
        $this->login()->loginNami()->withoutExceptionHandling();
        $participant = Participant::factory()->data(['vorname' => 'Max', 'select' => ['A', 'B']])
            ->for(Form::factory()->sections([
                FormtemplateSectionRequest::new()->name('Sektion')->fields([
                    $this->textField('vorname')->name('Vorname'),
                    $this->checkboxesField('select')->options(['A', 'B', 'C']),
                ])

            ]))
            ->create();

        $this->callFilter('participant.fields', [], ['participant' => $participant->id])
            ->assertOk()
            ->assertJsonPath('data.id', $participant->id)
            ->assertJsonPath('data.config.sections.0.name', 'Sektion')
            ->assertJsonPath('data.config.sections.0.fields.0.key', 'vorname')
            ->assertJsonPath('data.config.sections.0.fields.0.value', 'Max')
            ->assertJsonPath('data.config.sections.0.fields.1.key', 'select')
            ->assertJsonPath('data.config.sections.0.fields.1.value', ['A', 'B']);
    }
}
