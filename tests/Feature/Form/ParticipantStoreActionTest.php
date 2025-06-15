<?php

namespace Tests\Feature\Form;

use App\Form\Actions\ExportSyncAction;
use App\Form\Models\Form;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Queue;
use Tests\Lib\CreatesFormFields;

uses(DatabaseTransactions::class);
uses(CreatesFormFields::class);

beforeEach(function () {
    test()->setUpForm();
});

it('testItStoresParticipant', function () {
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
});

it('testItHasValidation', function () {
    Queue::fake();
    $this->login()->loginNami();
    $form = Form::factory()->fields([
        $this->textField('vorname')->name('Vorname')->required(true),
    ])
        ->create();

    $this->postJson(route('form.participant.store', ['form' => $form->id]), ['vorname' => ''])
        ->assertJsonValidationErrors(['vorname' => 'Vorname ist erforderlich.']);
});
