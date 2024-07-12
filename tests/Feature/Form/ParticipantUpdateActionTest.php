<?php

namespace Tests\Feature\Form;

use App\Form\Models\Form;
use App\Form\Models\Participant;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ParticipantUpdateActionTest extends FormTestCase
{

    use DatabaseTransactions;

    public function testItUpdatesParticipant(): void
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

        $this->patchJson(route('participant.update', ['participant' => $participant->id]), ['data' => ['vorname' => 'Jane']])
            ->assertOk();

        $this->assertEquals('Jane', $participant->fresh()->data['vorname']);
    }
}
