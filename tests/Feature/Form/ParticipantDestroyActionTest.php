<?php

namespace Tests\Feature\Form;

use App\Form\Models\Form;
use App\Form\Models\Participant;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Lib\CreatesFormFields;

uses(DatabaseTransactions::class);
uses(CreatesFormFields::class);

beforeEach(function () {
    test()->setUpForm();
});

it('testItCanDestroyAParticipant', function () {
    $this->login()->loginNami()->withoutExceptionHandling();
    $form = Form::factory()
        ->has(Participant::factory())
        ->sections([])
        ->create();

    $this->deleteJson(route('participant.destroy', ['participant' => $form->participants->first()]))
        ->assertOk();

    $this->assertDatabaseCount('participants', 1);
    $this->assertDatabaseHas('participants', [
        'cancelled_at' => now(),
        'id' => $form->participants->first()->id,
    ]);
});
