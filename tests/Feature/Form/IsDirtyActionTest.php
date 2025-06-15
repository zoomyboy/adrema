<?php

namespace Tests\Feature\Form;

use App\Form\Models\Form;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\Lib\CreatesFormFields;

uses(DatabaseTransactions::class);
uses(CreatesFormFields::class);

beforeEach(function () {
    test()->setUpForm();
});

it('testItChecksIfFormIsDirty', function () {
    $this->login()->loginNami();
    $form = Form::factory()->fields([
        $this->textField(),
    ])->create();

    $this->postJson(route('form.is-dirty', ['form' => $form]), ['config' => $form->config->toArray()])->assertJsonPath('result', false);

    $modifiedConfig = $form->config->toArray();
    data_set($modifiedConfig, 'sections.0.name', 'mod');
    $this->postJson(route('form.is-dirty', ['form' => $form]), ['config' => $modifiedConfig])->assertJsonPath('result', true);
});
