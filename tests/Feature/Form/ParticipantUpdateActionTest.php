<?php

namespace Tests\Feature\Form;

use App\Form\Actions\ExportSyncAction;
use App\Form\Models\Form;
use App\Form\Models\Participant;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Queue;
use Tests\Lib\CreatesFormFields;

uses(DatabaseTransactions::class);
uses(CreatesFormFields::class);

beforeEach(function () {
    test()->setUpForm();
});

it('testItUpdatesParticipant', function () {
    Queue::fake();
    $this->login()->loginNami()->withoutExceptionHandling();
    $participant = Participant::factory()->data(['vorname' => 'Max'])
        ->for(Form::factory()->fields([
            $this->textField('vorname')->name('Vorname'),
        ]))
        ->create();

    $this->patchJson(route('participant.update', ['participant' => $participant->id]), ['vorname' => 'Jane'])
        ->assertOk();

    $this->assertEquals('Jane', $participant->fresh()->data['vorname']);
    ExportSyncAction::assertPushed();
});

it('testItHasValidation', function () {
    $this->login()->loginNami();
    $participant = Participant::factory()->data(['vorname' => 'Max', 'select' => ['A', 'B']])
        ->for(Form::factory()->fields([
            $this->textField('vorname')->name('Vorname')->required(true),
        ]))
        ->create();

    $this->patchJson(route('participant.update', ['participant' => $participant->id]), ['vorname' => ''])
        ->assertJsonValidationErrors(['vorname' => 'Vorname ist erforderlich.']);
});
