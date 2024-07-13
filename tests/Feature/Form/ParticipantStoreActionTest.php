<?php

namespace Tests\Feature\Form;

use App\Form\Actions\ExportSyncAction;
use App\Form\Models\Form;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Queue;

class ParticipantStoreActionTest extends FormTestCase
{

    use DatabaseTransactions;

    public function testItStoresParticipant(): void
    {
        Queue::fake();
        $this->login()->loginNami()->withoutExceptionHandling();
        $form = Form::factory()->fields([
            $this->textField('vorname')->name('Vorname')->required(true),
        ])
            ->create();

        $this->postJson(route('form.participant.store', ['form' => $form->id]), ['vorname' => 'Jane'])
            ->assertOk();

        $this->assertEquals('Jane', $form->participants->first()->data['vorname']);
        ExportSyncAction::assertPushed();
    }

    public function testItHasValidation(): void
    {
        Queue::fake();
        $this->login()->loginNami();
        $form = Form::factory()->fields([
            $this->textField('vorname')->name('Vorname')->required(true),
        ])
            ->create();

        $this->postJson(route('form.participant.store', ['form' => $form->id]), ['vorname' => ''])
            ->assertJsonValidationErrors(['vorname' => 'Vorname ist erforderlich.']);
    }
}
