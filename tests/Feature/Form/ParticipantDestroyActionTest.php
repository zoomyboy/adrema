<?php

namespace Tests\Feature\Form;

use App\Form\Models\Form;
use App\Form\Models\Participant;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ParticipantDestroyActionTest extends FormTestCase
{

    use DatabaseTransactions;

    public function testItCanDestroyAParticipant(): void
    {
        $this->login()->loginNami()->withoutExceptionHandling();
        $form = Form::factory()
            ->has(Participant::factory())
            ->sections([])
            ->create();

        $this->deleteJson(route('participant.destroy', ['participant' => $form->participants->first()]))
            ->assertOk();

        $this->assertDatabaseCount('participants', 0);
    }
}
